<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\MessagingSystemConfiguration;
use SimplyCodedSoftware\IntegrationMessaging\Handler\Enricher\ExpressionEvaluationService;
use SimplyCodedSoftware\IntegrationMessaging\Handler\ReferenceSearchService;
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
    const MESSAGING_SYSTEM_CONFIGURATION = 'messaging_system_configuration';

    public function build(ContainerBuilder $container)
    {
        $container->setAlias('doctrineEntityManager-proxy', 'doctrine.orm.default_entity_manager')->setPublic(true);
        $configurationObserver = ContainerConfiguratorForMessagingObserver::create();
        $this->configureMessaging($container, $configurationObserver);

        foreach ($configurationObserver->getRequiredReferences() as $requiredReference) {
            $container->setAlias($requiredReference . '-proxy', $requiredReference)->setPublic(true);
        }

        foreach ($configurationObserver->getRegisteredGateways() as $referenceName => $interface) {
            $definition = new Definition();
            $definition->setFactory([ProxyGenerator::class, 'createFor']);
            $definition->setClass($interface);
            $definition->setArgument(0, $referenceName);
            $definition->setArgument(1, new Reference('service_container'));

            $container->setDefinition($referenceName, $definition);
        }

        $expressionLanguageCache = ExpressionEvaluationService::REFERENCE . "_cache";
        $definition = new Definition();
        $definition->setClass(FilesystemAdapter::class);
        $definition->setArgument(0, "");
        $definition->setArgument(1, "");
        $definition->setArgument(2, $container->get('kernel.cache_dir'));
        $this->container->set($expressionLanguageCache, $definition);

        $expressionLanguageAdapter = ExpressionEvaluationService::REFERENCE . "_adapter";
        $definition = new Definition();
        $definition->setClass(ExpressionLanguage::class);
        $definition->setArgument(0, new Reference($expressionLanguageCache));
        $container->setDefinition($expressionLanguageAdapter, $definition);

        $definition = new Definition();
        $definition->setClass(ExpressionLanguageAdapter::class);
        $definition->setArgument(0, new Reference($expressionLanguageAdapter));
        $container->setDefinition(ExpressionEvaluationService::REFERENCE, $definition);
    }


    public function boot()
    {
        /** @var MessagingSystemConfiguration $messagingSystemConfiguration */
        $configurationObserver = new NullConfigurationObserver();

        $this->buildMessagingSystemFrom($this->container, $this->configureMessaging($this->container, $configurationObserver));
    }

    /**
     * @param Container $container
     * @param $configurationObserver
     * @return MessagingSystemConfiguration|SymfonyMessagingSystem
     */
    private function configureMessaging(Container $container, $configurationObserver): MessagingSystemConfiguration
    {
        return SymfonyMessagingSystem::configure($container, $configurationObserver);
    }

    /**
     * @param Container $container
     * @param MessagingSystemConfiguration $messagingSystemConfiguration
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

            public function findByReference(string $reference)
            {
                return $this->container->get($reference . '-proxy');
            }
        });

        $this->container->set('messaging_system', $messagingSystem);
    }
}