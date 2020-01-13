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
            ->getRootNode()
                ->children()
                    ->booleanNode("loadSrc")
                        ->defaultTrue()
                    ->end()
                    ->booleanNode("failFast")
                        ->defaultTrue()
                    ->end()
                    ->scalarNode("serializationMediaType")
                        ->defaultNull()
                    ->end()
                    ->scalarNode("errorChannel")
                        ->defaultNull()
                    ->end()
                    ->arrayNode("namespaces")
                      ->scalarPrototype()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}