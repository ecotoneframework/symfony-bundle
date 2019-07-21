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
        $namespaces = array_merge(
            $container->hasParameter('messaging.application.context.namespace') ? $container->getParameter('messaging.application.context.namespace') : [],
            [FileSystemAnnotationRegistrationService::SIMPLY_CODED_SOFTWARE_NAMESPACE, FileSystemAnnotationRegistrationService::INTEGRATION_MESSAGING_NAMESPACE]
        );

        $messagingConfiguration =  MessagingSystemConfiguration::createWithCachedReferenceObjectsForNamespaces(
            realpath($container->getParameter('kernel.root_dir') . "/.."),
            $namespaces,
            new SymfonyReferenceTypeResolver($container),
            $container->getParameter("kernel.environment"),
            $container->getParameter("kernel.environment") === 'prod',
            true
        );

        foreach ($messagingConfiguration->getRegisteredGateways() as $referenceName => $interface) {
            $definition = new Definition();
            $definition->setFactory([ProxyGenerator::class, 'createFor']);
            $definition->setClass($interface);
            $definition->addArgument($referenceName);
            $definition->addArgument(new Reference('service_container'));
            $definition->addArgument($interface);
            $definition->setPublic(true);

            $container->setDefinition($referenceName, $definition);
        }

        foreach ($messagingConfiguration->getRequiredReferences() as $requiredReference) {
            $alias = $container->setAlias($requiredReference . '-proxy', $requiredReference);

            if ($alias) {
                $alias->setPublic(true);
            }
        }

        $container->setParameter(IntegrationMessagingBundle::MESSAGING_SYSTEM_CONFIGURATION_SERVICE_NAME, serialize($messagingConfiguration));
    }
}