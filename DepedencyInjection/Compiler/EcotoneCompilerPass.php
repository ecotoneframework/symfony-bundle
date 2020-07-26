<?php

namespace Ecotone\SymfonyBundle\DepedencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationException;
use Ecotone\AnnotationFinder\AnnotationFinder;
use Ecotone\AnnotationFinder\FileSystem\FileSystemAnnotationFinder;
use Ecotone\Messaging\Config\ApplicationConfiguration;
use Ecotone\Messaging\Config\ConfigurationException;
use Ecotone\Messaging\Config\MessagingSystemConfiguration;
use Ecotone\Messaging\Handler\ErrorHandler\RetryTemplateBuilder;
use Ecotone\Messaging\Handler\Gateway\ProxyFactory;
use Ecotone\Messaging\MessagingException;
use Ecotone\Messaging\Support\InvalidArgumentException;
use Ecotone\SymfonyBundle\EcotoneSymfonyBundle;
use Psr\Container\ContainerInterface;
use ReflectionException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class IntegrationMessagingCompilerPass
 * @package Ecotone\SymfonyBundle
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class EcotoneCompilerPass implements CompilerPassInterface
{
    const         FRAMEWORK_NAMESPACE   = 'Ecotone';
    public const WORKING_NAMESPACES_CONFIG = "ecotone.namespaces";
    public const FAIL_FAST_CONFIG = "ecotone.fail_fast";
    public const LOAD_SRC = "ecotone.load_src";
    public const DEFAULT_SERIALIZATION_MEDIA_TYPE = "ecotone.serializationMediaType";
    public const ERROR_CHANNEL = "ecotone.errorChannel";
    public const DEFAULT_MEMORY_LIMIT = "ecotone.defaultMemoryLimit";
    public const DEFAULT_CONNECTION_EXCEPTION_RETRY = "ecotone.defaultChannelPollRetry";
    const SRC_CATALOG = "src";

    /**
     * @param Container $container
     * @return bool|string
     */
    public static function getRootProjectPath(Container $container)
    {
        return realpath(($container->hasParameter('kernel.project_dir') ? $container->getParameter('kernel.project_dir') : $container->getParameter('kernel.root_dir') . "/.."));
    }

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
        $ecotoneCacheDirectory       = $container->getParameter("kernel.cache_dir") . DIRECTORY_SEPARATOR . "ecotone";
        $applicationConfiguration = ApplicationConfiguration::createWithDefaults()
            ->withEnvironment($container->getParameter("kernel.environment"))
            ->withFailFast($container->getParameter("kernel.environment") === "prod" ? false : $container->getParameter(self::FAIL_FAST_CONFIG))
            ->withLoadCatalog($container->getParameter(self::LOAD_SRC) ? "src" : "")
            ->withNamespaces(array_merge(
                $container->getParameter(self::WORKING_NAMESPACES_CONFIG),
                [self::FRAMEWORK_NAMESPACE]
            ))
            ->withCacheDirectoryPath($ecotoneCacheDirectory);

        if ($container->getParameter(self::DEFAULT_SERIALIZATION_MEDIA_TYPE)) {
            $applicationConfiguration = $applicationConfiguration
                                        ->withDefaultSerializationMediaType($container->getParameter(self::DEFAULT_SERIALIZATION_MEDIA_TYPE));
        }
        if ($container->getParameter(self::DEFAULT_MEMORY_LIMIT)) {
            $applicationConfiguration = $applicationConfiguration
                ->withConsumerMemoryLimit($container->getParameter(self::DEFAULT_MEMORY_LIMIT));
        }
        if ($container->getParameter(self::DEFAULT_CONNECTION_EXCEPTION_RETRY)) {
            $retryTemplate = $container->getParameter(self::DEFAULT_CONNECTION_EXCEPTION_RETRY);
            $applicationConfiguration = $applicationConfiguration
                ->withConnectionRetryTemplate(RetryTemplateBuilder::exponentialBackoffWithMaxDelay(
                    $retryTemplate["initialDelay"],
                    $retryTemplate["maxAttempts"],
                    $retryTemplate["multiplier"]
                ));
        }
        if ($container->getParameter(self::ERROR_CHANNEL)) {
            $applicationConfiguration = $applicationConfiguration
                                        ->withDefaultErrorChannel($container->getParameter(self::ERROR_CHANNEL));
        }

        $messagingConfiguration = MessagingSystemConfiguration::prepare(
            self::getRootProjectPath($container),
            new SymfonyReferenceTypeResolver($container),
            $applicationConfiguration
        );

        $definition = new Definition();
        $definition->setClass(SymfonyReferenceSearchService::class);
        $definition->setPublic(true);
        $definition->addArgument(new Reference('service_container'));
        $container->setDefinition("symfonyReferenceSearchService", $definition);

        foreach ($messagingConfiguration->getRegisteredGateways() as $gatewayProxyBuilder) {
            $definition = new Definition();
            $definition->setFactory([ProxyGenerator::class, 'createFor']);
            $definition->setClass($gatewayProxyBuilder->getInterfaceName());
            $definition->addArgument($gatewayProxyBuilder->getReferenceName());
            $definition->addArgument(new Reference('service_container'));
            $definition->addArgument($gatewayProxyBuilder->getInterfaceName());
            $definition->addArgument($ecotoneCacheDirectory);
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

        foreach ($messagingConfiguration->getOptionalReferences() as $requiredReference) {
            if ($container->has($requiredReference)) {
                $alias = $container->setAlias($requiredReference . '-proxy', $requiredReference);

                if ($alias) {
                    $alias->setPublic(true);
                }
            }
        }

        $container->setParameter(EcotoneSymfonyBundle::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME, serialize($applicationConfiguration));
    }
}