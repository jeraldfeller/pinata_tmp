<?php

namespace Vivo\AdminBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vivo_admin');

        $rootNode
            ->children()
                ->scalarNode('firewall_name')->defaultValue('admin_area')->cannotBeEmpty()->end()
                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('group')->defaultValue('Vivo\AdminBundle\Model\Group')->cannotBeEmpty()->end()
                        ->scalarNode('user')->defaultValue('Vivo\AdminBundle\Model\User')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('group')->defaultValue('Vivo\AdminBundle\Repository\GroupRepository')->cannotBeEmpty()->end()
                        ->scalarNode('user')->defaultValue('Vivo\AdminBundle\Repository\UserRepository')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->integerNode('reset_ttl')->min(600)->max(43200)->defaultValue(3600)->end()
                ->arrayNode('password_expiry')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')->defaultTrue()->isRequired()->end()
                        ->scalarNode('route')->defaultValue('vivo_admin.profile.password_expired')->cannotBeEmpty()->end()
                        ->scalarNode('target_query_parameter')->defaultValue('_target')->cannotBeEmpty()->end()
                        ->scalarNode('csrf_query_parameter')->defaultValue('_csrf')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->variableNode('roles')->defaultValue(array())->end()
                ->scalarNode('menu_class')->defaultValue('Vivo\AdminBundle\Menu\MainMenuBuilder')->cannotBeEmpty()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
