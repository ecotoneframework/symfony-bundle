<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\FileCacheReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\AnnotationModuleRetrievingService;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\FileSystemAnnotationRegistrationService;
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
    /**
     * @param Container             $container
     * @param ConfigurationObserver $configurationObserver
     *
     * @return MessagingSystemConfiguration
     */
    public static function configure(Container $container, ConfigurationObserver $configurationObserver): MessagingSystemConfiguration
    {
        $annotationReader = new FileCacheReader(
            new AnnotationReader(),
            $container->getParameter('kernel.cache_dir') . DIRECTORY_SEPARATOR . $container->getParameter("kernel.environment"),
            $container->getParameter("kernel.environment") == 'prod'
        );

        $namespaces = array_merge(
            $container->hasParameter('messaging.application.context.namespace') ? $container->getParameter('messaging.application.context.namespace') : [],
            [FileSystemAnnotationRegistrationService::SIMPLY_CODED_SOFTWARE_NAMESPACE, FileSystemAnnotationRegistrationService::INTEGRATION_MESSAGING_NAMESPACE]
        );

        return MessagingSystemConfiguration::prepareWitObserver(
            new AnnotationModuleRetrievingService(
                new FileSystemAnnotationRegistrationService(
                    $annotationReader,
                    realpath($container->getParameter('kernel.root_dir') . "/.."),
                    $namespaces,
                    true
                )
            ),
            $configurationObserver
        );
    }
}