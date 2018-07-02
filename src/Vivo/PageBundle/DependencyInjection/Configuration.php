<?php

namespace Vivo\PageBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vivo_page');

        $rootNode
            ->children()
                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('asset')->defaultValue('Vivo\PageBundle\Model\Asset')->cannotBeEmpty()->end()
                        ->scalarNode('asset_group')->defaultValue('Vivo\PageBundle\Model\AssetGroup')->cannotBeEmpty()->end()
                        ->scalarNode('content')->defaultValue('Vivo\PageBundle\Model\Content')->cannotBeEmpty()->end()
                        ->scalarNode('menu_node')->defaultValue('Vivo\PageBundle\Model\MenuNode')->cannotBeEmpty()->end()
                        ->scalarNode('page')->defaultValue('Vivo\PageBundle\Model\Page')->cannotBeEmpty()->end()
                        ->scalarNode('slug')->defaultValue('Vivo\PageBundle\Model\Slug')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('asset')->defaultValue('Vivo\PageBundle\Repository\AssetRepository')->cannotBeEmpty()->end()
                        ->scalarNode('asset_group')->defaultValue('Vivo\PageBundle\Repository\AssetGroupRepository')->cannotBeEmpty()->end()
                        ->scalarNode('content')->defaultValue('Vivo\PageBundle\Repository\ContentRepository')->cannotBeEmpty()->end()
                        ->scalarNode('menu_node')->defaultValue('Vivo\PageBundle\Repository\MenuNodeRepository')->cannotBeEmpty()->end()
                        ->scalarNode('page')->defaultValue('Vivo\PageBundle\Repository\PageRepository')->cannotBeEmpty()->end()
                        ->scalarNode('slug')->defaultValue('Vivo\PageBundle\Repository\SlugRepository')->cannotBeEmpty()->end()
                ->end()
                ->end()
                ->arrayNode('page_type')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('homepage')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Vivo\PageBundle\PageType\Type\HomepagePageType')->end()
                                ->arrayNode('blocks')
                                    ->addDefaultsIfNotSet()
                                    ->append(self::getContentBlocksNode())
                                    ->append(self::getAssetGroupBlocksNode())
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('standard')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Vivo\PageBundle\PageType\Type\StandardPageType')->end()
                                ->arrayNode('blocks')
                                    ->addDefaultsIfNotSet()
                                    ->append(self::getContentBlocksNode())
                                    ->append(self::getAssetGroupBlocksNode())
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('placeholder')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('class')->cannotBeEmpty()->defaultValue('Vivo\PageBundle\PageType\Type\PlaceholderPageType')->end()
                                ->arrayNode('blocks')
                                ->addDefaultsIfNotSet()
                                ->append(self::getContentBlocksNode())
                                ->append(self::getAssetGroupBlocksNode())
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        return $treeBuilder;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public static function getContentBlocksNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('content');

        $node
            ->useAttributeAsKey('alias')
            ->prototype('array')
                ->beforeNormalization()
                    ->ifString()
                    ->then(function ($v) { return array('name' => $v); })
                ->end()
                ->children()
                    ->scalarNode('class')->defaultValue('%vivo_page.model.content%')->end()
                    ->scalarNode('alias')->cannotBeEmpty()->end()
                    ->scalarNode('name')->isRequired()->end()
                    ->variableNode('options')->defaultValue(array())->end()
                    ->scalarNode('form_type')->defaultValue('Trsteel\CkeditorBundle\Form\Type\CkeditorType')->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public static function getAssetGroupBlocksNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('asset_group');

        $node
            ->useAttributeAsKey('alias')
            ->prototype('array')
                ->beforeNormalization()
                    ->ifString()
                    ->then(function ($v) { return array('name' => $v); })
                ->end()
                ->children()
                    ->scalarNode('class')->defaultValue('%vivo_page.model.asset_group%')->end()
                    ->scalarNode('alias')->cannotBeEmpty()->end()
                    ->scalarNode('name')->cannotBeEmpty()->isRequired()->end()
                    ->variableNode('options')->defaultValue(array())->end()
                    ->scalarNode('form_type')->defaultValue('Vivo\AssetBundle\Form\Type\AssetImageCollectionType')->cannotBeEmpty()->end()
                ->end()
            ->end()
        ;

        return $node;
    }
}
