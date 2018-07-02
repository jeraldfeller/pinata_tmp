<?php

namespace Vivo\UtilBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('vivo_util');

        $rootNode
            ->children()
                ->arrayNode('geoip')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('city_database')->defaultValue('/usr/local/share/GeoIP/GeoLite2-City.mmdb')->end()
                        ->scalarNode('country_database')->defaultValue('/usr/local/share/GeoIP/GeoLite2-Country.mmdb')->end()
                        ->scalarNode('cache_dir')->defaultValue('%kernel.cache_dir%/geoip')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('mapped_superclasses')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('repository')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
