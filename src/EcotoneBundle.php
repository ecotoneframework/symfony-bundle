<?php

namespace Ecotone\Symfony;

use Ecotone\Messaging\Config\ConfiguredMessagingSystem;
use Ecotone\Messaging\Config\MessagingSystemConfiguration;
use Ecotone\Messaging\Conversion\MediaType;
use Ecotone\Messaging\Endpoint\NoConsumerFactoryForBuilderException;
use Ecotone\Messaging\Handler\ExpressionEvaluationService;
use Ecotone\Messaging\Handler\Gateway\GatewayProxyConfiguration;
use Ecotone\Messaging\Handler\ReferenceSearchService;
use Ecotone\Messaging\Handler\SymfonyExpressionEvaluationAdapter;
use Ecotone\Messaging\MessagingException;
use Ecotone\Symfony\DepedencyInjection\EcotoneExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Throwable;

/**
 * Class IntegrationMessagingBundle
 * @package Ecotone\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class EcotoneBundle extends Bundle
{
    const MESSAGING_SYSTEM_SERVICE_NAME = "messaging_system";
    const MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME = "messaging_system_configuration";

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new EcotoneCompilerPass());

        $this->setUpExpressionLanguage($container);

        $definition = new Definition();
        $definition->setClass(ConfiguredMessagingSystem::class);
        $definition->setSynthetic(true);
        $definition->setPublic(true);
        $container->setDefinition(self::MESSAGING_SYSTEM_SERVICE_NAME, $definition);

        $definition = new Definition();
        $definition->setClass(SymfonyReferenceSearchService::class);
        $definition->setPublic(true);
        $definition->addArgument(new Reference('service_container'));
        $container->setDefinition("symfonyReferenceSearchService", $definition);

        $definition = new Definition();
        $definition->setClass(ListAllPollableEndpointsCommand::class);
        $definition->addArgument(new Reference(self::MESSAGING_SYSTEM_SERVICE_NAME));
        $definition->addTag('console.command');
        $container->setDefinition(ListAllPollableEndpointsCommand::class, $definition);

        $definition = new Definition();
        $definition->setClass(RunPollableEndpointCommand::class);
        $definition->addArgument(new Reference(self::MESSAGING_SYSTEM_SERVICE_NAME));
        $definition->addTag('console.command');
        $container->setDefinition(RunPollableEndpointCommand::class, $definition);
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
        $messagingSystem = (
        unserialize(file_get_contents($this->container->getParameter(self::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME)))
        )->buildMessagingSystemFromConfiguration($this->container->get('symfonyReferenceSearchService'));


        $this->container->set(self::MESSAGING_SYSTEM_SERVICE_NAME, $messagingSystem);
    }

    public function getContainerExtension()
    {
        return new EcotoneExtension();
    }
}