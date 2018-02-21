<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\AnnotationModuleConfigurationRetrievingService;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\DoctrineClassMetadataReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\FileSystemClassLocator;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationObserver;
use SimplyCodedSoftware\IntegrationMessaging\Config\MessagingSystemConfiguration;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class SymfonyMessagingSystem
 * @package App\MessagingBundle
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class SymfonyMessagingSystem
{
    public static function configure(Container $container, ConfigurationObserver $configurationObserver): MessagingSystemConfiguration
    {
        $variableConfigurationRetrievingService = new VariableConfigurationRetrievingService($container);
        $annotationReader = new FileCacheReader(
            new AnnotationReader(),
            $variableConfigurationRetrievingService->get('kernel.cache_dir'),
            $variableConfigurationRetrievingService->get("kernel.environment") == 'prod'
        );

        return MessagingSystemConfiguration::prepare(
            new AnnotationModuleConfigurationRetrievingService(
                $variableConfigurationRetrievingService,
                $configurationObserver,
                new ContainerClassLocator(
                    $container, $annotationReader
                ),
                new ContainerClassMetadataReader(
                    $container,
                    new DoctrineClassMetadataReader($annotationReader)
                )
            ),
            $variableConfigurationRetrievingService,
            $configurationObserver
        );
    }
}