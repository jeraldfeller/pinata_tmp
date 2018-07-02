<?php

namespace Vivo\SiteBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vivo_site');

        $rootNode
            ->children()
            ->booleanNode('fallback_to_primary_site')->defaultValue(false)->end()
            ->variableNode('live_environments')->defaultValue(array('prod'))->end()
            ->arrayNode('site_password')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultTrue()->end()
                    ->variableNode('passwords')
                        ->beforeNormalization()
                        ->ifString()
                            ->then(function ($v) { return array($v); })
                        ->end()
                        ->defaultValue(array())
                    ->end()
                    ->scalarNode('root_password')->defaultValue('v!vo')->cannotBeEmpty()->end()
                    ->scalarNode('route')->defaultValue('vivo_site.site.site_password')->cannotBeEmpty()->end()
                    ->scalarNode('target_query_parameter')->defaultValue('_target')->cannotBeEmpty()->end()
                    ->scalarNode('csrf_query_parameter')->defaultValue('_csrf')->cannotBeEmpty()->end()
                    ->scalarNode('salt')->defaultValue('%secret%')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('mailers')
                ->useAttributeAsKey('id')
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('mailer_id')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('google')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('enabled')->defaultTrue()->end()
                ->end()
            ->end()
            ->arrayNode('model')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('site')->defaultValue('Vivo\SiteBundle\Model\Site')->cannotBeEmpty()->end()
                    ->scalarNode('domain')->defaultValue('Vivo\SiteBundle\Model\Domain')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('repository')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('site')->defaultValue('Vivo\SiteBundle\Repository\SiteRepository')->cannotBeEmpty()->end()
                    ->scalarNode('domain')->defaultValue('Vivo\SiteBundle\Repository\DomainRepository')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('devices')
                ->addDefaultsIfNotSet()
                ->children()
                    ->booleanNode('tablet')->defaultFalse()->end()
                    ->booleanNode('mobile')->defaultFalse()->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
