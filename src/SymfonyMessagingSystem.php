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
    /**
     * @var MessagingSystemConfiguration
     */
    private static $messagingSystemConfiguration;

    public static function configure(string $projectRootPath, VariableConfigurationRetrievingService $variableConfigurationRetrievingService, ConfigurationObserver $configurationObserver): MessagingSystemConfiguration
    {
        if (!self::$messagingSystemConfiguration) {
            $annotationReader = new FileCacheReader(
                new AnnotationReader(),
                $variableConfigurationRetrievingService->get('kernel.cache_dir'),
                $debug = true
            );
            $messagingSystemConfiguration = MessagingSystemConfiguration::prepare(
                new AnnotationModuleConfigurationRetrievingService(
                    $variableConfigurationRetrievingService,
                    $configurationObserver,
                    new FileSystemClassLocator(
                        $annotationReader,
                        [
                            $projectRootPath,
                            $projectRootPath . DIRECTORY_SEPARATOR . "../vendor"
                        ],
                        [
                            FileSystemClassLocator::SIMPLY_CODED_SOFTWARE_NAMESPACE,
                            FileSystemClassLocator::INTEGRATION_MESSAGING_NAMESPACE
                        ]
                    ),
                    new DoctrineClassMetadataReader($annotationReader)
                ),
                $variableConfigurationRetrievingService,
                $configurationObserver
            );

            self::$messagingSystemConfiguration = $messagingSystemConfiguration;
        }


        return self::$messagingSystemConfiguration;
    }
}