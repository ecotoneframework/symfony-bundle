<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ecotone');

//        symfony 3
        if (method_exists($treeBuilder, "root")) {
            $treeBuilder->root("ecotone")
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
                ->scalarPrototype()->end()
                ->end()
                ->arrayNode("pollableAnnotations")
                ->children()
                ->scalarNode('class')
                ->cannotBeEmpty()
                ->end()
                ->scalarNode('method')
                ->cannotBeEmpty()
                ->end()
                ->end()
                ->end()
                ->end()
                ->end();
        } else {
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
                ->scalarPrototype()->end()
                ->end()
                ->arrayNode("pollableAnnotations")
                ->children()
                ->scalarNode('class')
                ->cannotBeEmpty()
                ->end()
                ->scalarNode('method')
                ->cannotBeEmpty()
                ->end()
                ->end()
                ->end()
                ->end()
                ->end();
        }

        return $treeBuilder;
    }
}