<?php

namespace Ecotone\Symfony;

use Doctrine\Common\Annotations\AnnotationException;
use Ecotone\Messaging\Config\Annotation\FileSystemAnnotationRegistrationService;
use Ecotone\Messaging\Config\ConfigurationException;
use Ecotone\Messaging\Config\MessagingSystemConfiguration;
use Ecotone\Messaging\Handler\Gateway\ProxyFactory;
use Ecotone\Messaging\MessagingException;
use Ecotone\Messaging\Support\InvalidArgumentException;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class IntegrationMessagingCompilerPass
 * @package Ecotone\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class EcotoneCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @return void
     * @throws AnnotationException
     * @throws ConfigurationException
     * @throws MessagingException
     * @throws InvalidArgumentException
     * @throws ReflectionException
     */
    public function process(ContainerBuilder $container)
    {
        $namespaces = array_merge(
            $container->hasParameter('messaging.application.context.namespace') ? $container->getParameter('messaging.application.context.namespace') : [],
            [FileSystemAnnotationRegistrationService::FRAMEWORK_NAMESPACE]
        );
        $isLazyLoaded = $container->getParameter("kernel.environment") === 'prod';

        $messagingConfiguration = MessagingSystemConfiguration::createWithCachedReferenceObjectsForNamespaces(
            realpath($container->getParameter('kernel.root_dir') . "/.."),
            $namespaces,
            new SymfonyReferenceTypeResolver($container),
            $container->getParameter("kernel.environment"),
            $isLazyLoaded,
            true,
            ProxyFactory::createWithCache($container->getParameter("kernel.cache_dir"))
        );

        foreach ($messagingConfiguration->getRegisteredGateways() as $gatewayProxyBuilder) {
            $definition = new Definition();
            $definition->setFactory([ProxyGenerator::class, 'createFor']);
            $definition->setClass($gatewayProxyBuilder->getInterfaceName());
            $definition->addArgument($gatewayProxyBuilder->getReferenceName());
            $definition->addArgument(new Reference('service_container'));
            $definition->addArgument($gatewayProxyBuilder->getInterfaceName());
            $definition->addArgument("%kernel.cache_dir%");
            $definition->addArgument($isLazyLoaded);
            $definition->setPublic(true);

            $container->setDefinition($gatewayProxyBuilder->getReferenceName(), $definition);
        }

        foreach ($messagingConfiguration->getRequiredReferences() as $requiredReference) {
            $alias = $container->setAlias($requiredReference . '-proxy', $requiredReference);

            if ($alias) {
                $alias->setPublic(true);
            }
        }

        $container->setParameter(EcotoneBundle::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME, serialize($messagingConfiguration));
    }
}