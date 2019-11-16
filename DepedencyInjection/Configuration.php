<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ecotone');

        $treeBuilder
            ->root("ecotone")
                ->children()
                    ->booleanNode("loadSrc")
                        ->defaultTrue()
                    ->end()
                    ->booleanNode("failFast")
                    ->end()
                    ->arrayNode("namespaces")
                        ->prototype('scalar')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}