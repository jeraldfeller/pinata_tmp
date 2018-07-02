<?php

namespace Vivo\SecurityBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vivo_security');

        $rootNode
            ->children()
                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('authentication_log')->defaultValue('Vivo\SecurityBundle\Model\AuthenticationLog')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('authentication_log')->defaultValue('Vivo\SecurityBundle\Repository\AuthenticationLogRepository')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('default')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->integerNode('hourly_ip_username_limit')->defaultValue(10)->end()
                        ->integerNode('daily_username_limit')->defaultValue(500)->end()
                        ->integerNode('hourly_ip_limit')->defaultValue(50)->end()
                        ->integerNode('daily_ip_limit')->defaultValue(500)->end()
                    ->end()
                ->end()
                ->arrayNode('firewalls')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')->cannotBeEmpty()->end()
                            ->booleanNode('enabled')->end()
                            ->integerNode('hourly_ip_username_limit')->end()
                            ->integerNode('daily_username_limit')->end()
                            ->integerNode('hourly_ip_limit')->end()
                            ->integerNode('daily_ip_limit')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
