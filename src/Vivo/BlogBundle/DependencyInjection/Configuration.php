<?php

namespace Vivo\BlogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Vivo\PageBundle\DependencyInjection\Configuration as PageConfiguration;

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
        $rootNode = $treeBuilder->root('vivo_blog');

        $rootNode
            ->children()
                ->booleanNode('include_author_field')->defaultFalse()->end()
                ->arrayNode('page_type')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('blog')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Vivo\BlogBundle\PageType\BlogPageType')->end()
                                ->arrayNode('blocks')
                                    ->addDefaultsIfNotSet()
                                    ->append(PageConfiguration::getContentBlocksNode())
                                    ->append(PageConfiguration::getAssetGroupBlocksNode())
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('category')->defaultValue('Vivo\BlogBundle\Model\Category')->cannotBeEmpty()->end()
                        ->scalarNode('category_slug')->defaultValue('Vivo\BlogBundle\Model\CategorySlug')->cannotBeEmpty()->end()
                        ->scalarNode('post')->defaultValue('Vivo\BlogBundle\Model\Post')->cannotBeEmpty()->end()
                        ->scalarNode('post_slug')->defaultValue('Vivo\BlogBundle\Model\PostSlug')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('category')->defaultValue('Vivo\BlogBundle\Repository\CategoryRepository')->cannotBeEmpty()->end()
                        ->scalarNode('category_slug')->defaultValue('Vivo\BlogBundle\Repository\CategorySlugRepository')->cannotBeEmpty()->end()
                        ->scalarNode('post')->defaultValue('Vivo\BlogBundle\Repository\PostRepository')->cannotBeEmpty()->end()
                        ->scalarNode('post_slug')->defaultValue('Vivo\BlogBundle\Repository\PostSlugRepository')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }
}
