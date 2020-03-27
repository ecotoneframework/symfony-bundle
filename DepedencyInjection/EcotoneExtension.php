<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection;

use Ecotone\SymfonyBundle\DepedencyInjection\Compiler\EcotoneCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class EcotoneExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(EcotoneCompilerPass::WORKING_NAMESPACES_CONFIG, $config['namespaces']);
        $container->setParameter(EcotoneCompilerPass::FAIL_FAST_CONFIG, $config['failFast']);
        $container->setParameter(EcotoneCompilerPass::SERIALIZATION_DEFAULT_MEDIA_TYPE, $config['serializationMediaType']);
        $container->setParameter(EcotoneCompilerPass::ERROR_CHANNEL, $config['errorChannel']);
        if (isset($config['pollableAnnotations']) && $config['pollableAnnotations']) {
            $class = $config['pollableAnnotations']['class'];
            $method = $config['pollableAnnotations']['method'];
            new \ReflectionMethod($class, $method);
            $container->setParameter(EcotoneCompilerPass::POLLABLE_ENDPOINTS, serialize(forward_static_call([$class, $method])));
        }
    }
}