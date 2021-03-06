<?php

namespace WS\Site\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ws_site');
        $root = $treeBuilder->getRootNode();
        $root
            ->children()
                ->booleanNode('redirection')
                    ->defaultTrue()
                    ->info('Disables or Enables the Redirection service.')
                ->end() // redirection
                ->arrayNode('sitemap')
                    ->addDefaultsIfNotSet()
                    ->info('Allows to configure sitemap root location.')
                    ->children()
                        ->scalarNode('root')
                            ->defaultValue('public/site')
                        ->end()
                    ->end()
                ->end() // sitemap
            ->end()
        ;

        return $treeBuilder;
    }
}
