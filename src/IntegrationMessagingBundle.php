<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\AnnotationReader;
use SimplyCodedSoftware\Messaging\Config\Annotation\AnnotationModuleRetrievingService;
use SimplyCodedSoftware\Messaging\Config\Annotation\FileSystemAnnotationRegistrationService;
use SimplyCodedSoftware\Messaging\Config\Configuration;
use SimplyCodedSoftware\Messaging\Config\ConfiguredMessagingSystem;
use SimplyCodedSoftware\Messaging\Config\GatewayReference;
use SimplyCodedSoftware\Messaging\Config\MessagingSystemConfiguration;
use SimplyCodedSoftware\Messaging\Config\ReferenceTypeFromNameResolver;
use SimplyCodedSoftware\Messaging\Handler\ExpressionEvaluationService;
use SimplyCodedSoftware\Messaging\Handler\ReferenceSearchService;
use SimplyCodedSoftware\Messaging\Handler\SymfonyExpressionEvaluationAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IntegrationMessagingBundle
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class IntegrationMessagingBundle extends Bundle
{
    const MESSAGING_SYSTEM_SERVICE_NAME = "messaging_system";
    const MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME = "messaging_system_configuration";

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new IntegrationMessagingCompilerPass());

        $this->setUpExpressionLanguage($container);

        $definition = new Definition();
        $definition->setClass(ConfiguredMessagingSystem::class);
        $definition->setSynthetic(true);
        $definition->setPublic(true);
        $container->setDefinition(self::MESSAGING_SYSTEM_SERVICE_NAME, $definition);

        $definition = new Definition();
        $definition->setClass(ListAllAsynchronousConsumersCommand::class);
        $definition->setArgument(0, new Reference(self::MESSAGING_SYSTEM_SERVICE_NAME));
        $definition->addTag('console.command', array('command' => 'integration-messaging:list-all-async-consumers'));
        $container->setDefinition(ListAllAsynchronousConsumersCommand::class, $definition);

        $definition = new Definition();
        $definition->setClass(RunAsynchronousConsumerCommand::class);
        $definition->setArgument(0, new Reference(self::MESSAGING_SYSTEM_SERVICE_NAME));
        $definition->addTag('console.command', array('command' => 'integration-messaging:run-consumer'));
        $container->setDefinition(RunAsynchronousConsumerCommand::class, $definition);
    }


    public function boot()
    {
        $this->buildMessagingSystemFrom($this->container, unserialize($this->container->getParameter(self::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME)));
    }

    /**
     * @param Container $container
     * @param ReferenceTypeFromNameResolver $referenceTypeFromNameResolver
     * @return MessagingSystemConfiguration
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \SimplyCodedSoftware\Messaging\Config\ConfigurationException
     * @throws \SimplyCodedSoftware\Messaging\MessagingException
     */
    private function configureMessaging(Container $container, ReferenceTypeFromNameResolver $referenceTypeFromNameResolver): Configuration
    {
        $annotationReader = new AnnotationReader();

        $namespaces = array_merge(
            $container->hasParameter('messaging.application.context.namespace') ? $container->getParameter('messaging.application.context.namespace') : [],
            [FileSystemAnnotationRegistrationService::SIMPLY_CODED_SOFTWARE_NAMESPACE, FileSystemAnnotationRegistrationService::INTEGRATION_MESSAGING_NAMESPACE]
        );

        return MessagingSystemConfiguration::prepareWithCachedReferenceObjects(
            new AnnotationModuleRetrievingService(
                new FileSystemAnnotationRegistrationService(
                    $annotationReader,
                    realpath($container->getParameter('kernel.root_dir') . "/.."),
                    $namespaces,
                    $container->getParameter("kernel.environment"),
                    true
                )
            ),
            $referenceTypeFromNameResolver
        );
    }

    /**
     * @param Container $container
     * @param MessagingSystemConfiguration $messagingSystemConfiguration
     * @throws \SimplyCodedSoftware\Messaging\Endpoint\NoConsumerFactoryForBuilderException
     * @throws \SimplyCodedSoftware\Messaging\MessagingException
     */
    private function buildMessagingSystemFrom(Container $container, MessagingSystemConfiguration $messagingSystemConfiguration): void
    {
        $messagingSystem = $messagingSystemConfiguration->buildMessagingSystemFromConfiguration(new class($container) implements ReferenceSearchService
        {
            /**
             * @var Container
             */
            private $container;

            /**
             *  constructor.
             * @param Container $container
             */
            public function __construct(Container $container)
            {
                $this->container = $container;
            }

            public function get(string $reference)
            {
                return $this->container->get($reference . '-proxy');
            }
        });

        $this->container->set(self::MESSAGING_SYSTEM_SERVICE_NAME, $messagingSystem);

        /** @var GatewayReference $gateway */
        foreach ($messagingSystem->getGatewayList() as $gateway) {
            $this->container->set($gateway->getReferenceName(), $gateway->getGateway());
        }
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    private function setUpExpressionLanguage(ContainerBuilder $container): void
    {
        $expressionLanguageCache = ExpressionEvaluationService::REFERENCE . "_cache";
        $definition = new Definition();
        $definition->setClass(FilesystemAdapter::class);
        $definition->setArgument(0, "");
        $definition->setArgument(1, 0);
        $definition->setArgument(2, $container->getParameter('kernel.cache_dir'));

        $container->setDefinition($expressionLanguageCache, $definition);

        $expressionLanguageAdapter = ExpressionEvaluationService::REFERENCE . "_adapter";
        $definition = new Definition();
        $definition->setClass(ExpressionLanguage::class);
        $definition->setArgument(0, new Reference($expressionLanguageCache));

        $container->setDefinition($expressionLanguageAdapter, $definition);

        $definition = new Definition();
        $definition->setClass(SymfonyExpressionEvaluationAdapter::class);
        $definition->setFactory([SymfonyExpressionEvaluationAdapter::class, 'createWithExternalExpressionLanguage']);
        $definition->setArgument(0, new Reference($expressionLanguageAdapter));
        $definition->setPublic(true);

        $container->setDefinition(ExpressionEvaluationService::REFERENCE, $definition);
    }
}