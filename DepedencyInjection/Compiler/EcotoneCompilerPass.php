<?php

namespace Ecotone\Symfony\DepedencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationException;
use Ecotone\Messaging\Config\Annotation\FileSystemAnnotationRegistrationService;
use Ecotone\Messaging\Config\ConfigurationException;
use Ecotone\Messaging\Config\MessagingSystemConfiguration;
use Ecotone\Messaging\Handler\Gateway\ProxyFactory;
use Ecotone\Messaging\MessagingException;
use Ecotone\Messaging\Support\InvalidArgumentException;
use Ecotone\Symfony\EcotoneBundle;
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
    public const WORKING_NAMESPACES_CONFIG = "ecotone.namespaces";
    public const FAIL_FAST_CONFIG = "ecotone.fail_fast";
    public const LOAD_SRC = "ecotone.load_src";

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
            $container->getParameter(self::WORKING_NAMESPACES_CONFIG),
            [FileSystemAnnotationRegistrationService::FRAMEWORK_NAMESPACE]
        );

        $definition = new Definition();
        $definition->setClass(SymfonyReferenceSearchService::class);
        $definition->setPublic(true);
        $definition->addArgument(new Reference('service_container'));
        $container->setDefinition("symfonyReferenceSearchService", $definition);

        $messagingConfiguration = MessagingSystemConfiguration::createWithCachedReferenceObjectsForNamespaces(
            realpath($container->getParameter('kernel.root_dir') . "/.."),
            $namespaces,
            new SymfonyReferenceTypeResolver($container),
            $container->getParameter("kernel.environment"),
            $container->getParameter(self::FAIL_FAST_CONFIG),
            $container->getParameter(self::LOAD_SRC),
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
            $definition->addArgument($container->getParameter(self::FAIL_FAST_CONFIG));
            $definition->setPublic(true);

            $container->setDefinition($gatewayProxyBuilder->getReferenceName(), $definition);
        }

        foreach ($messagingConfiguration->getRequiredReferences() as $requiredReference) {
            $alias = $container->setAlias($requiredReference . '-proxy', $requiredReference);

            if ($alias) {
                $alias->setPublic(true);
            }
        }

        $path = $container->getParameter("kernel.cache_dir") . DIRECTORY_SEPARATOR . 'ecotoneMessagingConfiguration';
        file_put_contents($path, serialize($messagingConfiguration));
        $container->setParameter(EcotoneBundle::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME, $path);
    }
}