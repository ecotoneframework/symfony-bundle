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
            $treeBuilder
                ->root("ecotone")
                    ->children()
                        ->booleanNode("failFast")
                        ->defaultTrue()
                        ->end()

                        ->booleanNode("loadSrcNamespaces")
                        ->defaultTrue()
                        ->end()

                        ->scalarNode("serializationMediaType")
                        ->defaultNull()
                        ->end()

                        ->scalarNode("defaultErrorChannel")
                        ->defaultNull()
                        ->end()

                        ->arrayNode("namespaces")
                            ->scalarPrototype()
                            ->end()
                        ->end()

                        ->integerNode("defaultMemoryLimit")
                        ->isRequired()
                        ->end()

                        ->arrayNode("defaultChannelPollRetry")
                            ->children()
                                ->integerNode('initialDelay')
                                ->isRequired()
                                ->end()
                                ->integerNode('maxAttempts')
                                ->isRequired()
                                ->end()
                                ->integerNode('multiplier')
                                ->isRequired()
                                ->end()
                            ->end()
                        ->end()

                    ->end()
                ->end();
        } else {
            $treeBuilder
                ->getRootNode()
                    ->children()
                        ->booleanNode("failFast")
                        ->defaultTrue()
                        ->end()

                        ->booleanNode("loadSrcNamespaces")
                        ->defaultTrue()
                        ->end()

                        ->scalarNode("serializationMediaType")
                        ->defaultNull()
                        ->end()

                        ->scalarNode("defaultErrorChannel")
                        ->defaultNull()
                        ->end()


                        ->arrayNode("namespaces")
                            ->scalarPrototype()
                            ->end()
                        ->end()

                        ->integerNode("defaultMemoryLimit")
                        ->isRequired()
                        ->end()

                        ->arrayNode("defaultChannelPollRetry")
                            ->children()
                                ->integerNode('initialDelay')
                                ->isRequired()
                                ->end()

                                ->integerNode('maxAttempts')
                                ->isRequired()
                                ->end()

                                ->integerNode('multiplier')
                                ->isRequired()
                                ->end()
                                ->end()
                        ->end()

                    ->end()
                ->end();
        }

        return $treeBuilder;
    }
}