<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\AnnotationReader;
use SimplyCodedSoftware\Messaging\Config\Annotation\AnnotationModuleRetrievingService;
use SimplyCodedSoftware\Messaging\Config\Annotation\FileSystemAnnotationRegistrationService;
use SimplyCodedSoftware\Messaging\Config\Configuration;
use SimplyCodedSoftware\Messaging\Config\MessagingSystemConfiguration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class IntegrationMessagingCompilerPass
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class IntegrationMessagingCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @return Configuration
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \SimplyCodedSoftware\Messaging\Config\ConfigurationException
     * @throws \SimplyCodedSoftware\Messaging\Handler\TypeDefinitionException
     * @throws \SimplyCodedSoftware\Messaging\MessagingException
     */
    public function process(ContainerBuilder $container)
    {
        $annotationReader = new AnnotationReader();

        $namespaces = array_merge(
            $container->hasParameter('messaging.application.context.namespace') ? $container->getParameter('messaging.application.context.namespace') : [],
            [FileSystemAnnotationRegistrationService::SIMPLY_CODED_SOFTWARE_NAMESPACE, FileSystemAnnotationRegistrationService::INTEGRATION_MESSAGING_NAMESPACE]
        );

        $messagingConfiguration =  MessagingSystemConfiguration::prepareWithCachedReferenceObjects(
            new AnnotationModuleRetrievingService(
                new FileSystemAnnotationRegistrationService(
                    $annotationReader,
                    realpath($container->getParameter('kernel.root_dir') . "/.."),
                    $namespaces,
                    $container->getParameter("kernel.environment"),
                    true
                )
            ),
            new SymfonyReferenceTypeResolver($container)
        );

        foreach ($messagingConfiguration->getRegisteredGateways() as $referenceName => $interface) {
            $definition = new Definition();
            $definition->setFactory([ProxyGenerator::class, 'createFor']);
            $definition->setClass($interface);
            $definition->setArgument(0, $referenceName);
            $definition->setArgument(1, new Reference('service_container'));
            $definition->setArgument(2, $interface);
            $definition->setPublic(true);

            $container->setDefinition($referenceName, $definition);
        }

        foreach ($messagingConfiguration->getRequiredReferences() as $requiredReference) {
            $container->setAlias($requiredReference . '-proxy', $requiredReference)->setPublic(true);
        }

        $container->setParameter(IntegrationMessagingBundle::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME, serialize($messagingConfiguration));
    }
}