<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\Messaging\Config\ConfiguredMessagingSystem;
use SimplyCodedSoftware\Messaging\Config\MessagingSystemConfiguration;
use SimplyCodedSoftware\Messaging\Endpoint\NoConsumerFactoryForBuilderException;
use SimplyCodedSoftware\Messaging\Handler\ExpressionEvaluationService;
use SimplyCodedSoftware\Messaging\Handler\ReferenceSearchService;
use SimplyCodedSoftware\Messaging\Handler\SymfonyExpressionEvaluationAdapter;
use SimplyCodedSoftware\Messaging\MessagingException;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Throwable;

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
        $definition->addArgument(new Reference(self::MESSAGING_SYSTEM_SERVICE_NAME));
        $definition->addTag('console.command');
        $container->setDefinition(ListAllAsynchronousConsumersCommand::class, $definition);

        $definition = new Definition();
        $definition->setClass(RunAsynchronousConsumerCommand::class);
        $definition->addArgument(new Reference(self::MESSAGING_SYSTEM_SERVICE_NAME));
        $definition->addTag('console.command');
        $container->setDefinition(RunAsynchronousConsumerCommand::class, $definition);
    }

    /**
     * @param ContainerBuilder $container
     * @return void
     */
    private function setUpExpressionLanguage(ContainerBuilder $container): void
    {
        $expressionLanguageAdapter = ExpressionEvaluationService::REFERENCE . "_adapter";
        $definition = new Definition();
        $definition->setClass(ExpressionLanguage::class);

        $container->setDefinition($expressionLanguageAdapter, $definition);

        $definition = new Definition();
        $definition->setClass(SymfonyExpressionEvaluationAdapter::class);
        $definition->setFactory([SymfonyExpressionEvaluationAdapter::class, 'createWithExternalExpressionLanguage']);
        $definition->addArgument(new Reference($expressionLanguageAdapter));
        $definition->setPublic(true);

        $container->setDefinition(ExpressionEvaluationService::REFERENCE, $definition);
    }

    public function boot()
    {
        try {
            $this->buildMessagingSystemFrom($this->container, unserialize($this->container->getParameter(self::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME)));
        } catch (Throwable $e) {
            echo $e->getMessage();
            throw $e;
        }
    }

    /**
     * @param Container $container
     * @param MessagingSystemConfiguration $messagingSystemConfiguration
     * @throws NoConsumerFactoryForBuilderException
     * @throws MessagingException
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
    }
}