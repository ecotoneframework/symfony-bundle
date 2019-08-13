<?php


namespace Ecotone\Symfony\DepedencyInjection;


use Ecotone\Symfony\EcotoneCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class EcotoneExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(EcotoneCompilerPass::WORKING_NAMESPACES_CONFIG, $config['namespaces']);
        $container->setParameter(EcotoneCompilerPass::LOAD_SRC, $config['loadSrc']);
        $container->setParameter(EcotoneCompilerPass::FAIL_FAST_CONFIG, $config['failFast'] ?? $container->getParameter("kernel.environment") === 'prod');
    }
}