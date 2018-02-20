<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\AnnotationModuleConfigurationRetrievingService;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\DoctrineClassMetadataReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\FileSystemClassLocator;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationObserver;
use SimplyCodedSoftware\IntegrationMessaging\Config\MessagingSystemConfiguration;

/**
 * Class SymfonyMessagingSystem
 * @package App\MessagingBundle
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class SymfonyMessagingSystem
{
    public static function configure(string $messagingApplicationContextNamespace, string $projectRootPath, VariableConfigurationRetrievingService $variableConfigurationRetrievingService, ConfigurationObserver $configurationObserver): MessagingSystemConfiguration
    {
        $annotationReader = new FileCacheReader(
            new AnnotationReader(),
            $variableConfigurationRetrievingService->get('kernel.cache_dir'),
            $debug = true
        );


        return MessagingSystemConfiguration::prepare(
            new AnnotationModuleConfigurationRetrievingService(
                $variableConfigurationRetrievingService,
                $configurationObserver,
                new FileSystemClassLocator(
                    $annotationReader,
                    [
                        realpath($projectRootPath . "/.."),
                        realpath($projectRootPath . DIRECTORY_SEPARATOR . "../vendor")
                    ],
                    [
                        FileSystemClassLocator::SIMPLY_CODED_SOFTWARE_NAMESPACE,
                        FileSystemClassLocator::INTEGRATION_MESSAGING_NAMESPACE,
                        $messagingApplicationContextNamespace
                    ]
                ),
                new DoctrineClassMetadataReader($annotationReader)
            ),
            $variableConfigurationRetrievingService,
            $configurationObserver
        );
    }
}