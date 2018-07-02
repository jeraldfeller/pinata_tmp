<?php

namespace Vivo\AssetBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('vivo_asset');

        $rootNode
            ->children()
                // @TODO: check_download_context should be removed from v4.0 (@deprecated)
                ->booleanNode('check_download_context')->defaultFalse()->end()
                ->booleanNode('create_non_existent_images')->defaultFalse()->end()
                ->scalarNode('upload_directory')->defaultValue('%kernel.root_dir%/var/media')->cannotBeEmpty()->end()
                ->arrayNode('model')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('asset')->defaultValue('Vivo\AssetBundle\Model\Asset')->cannotBeEmpty()->end()
                        ->scalarNode('ckeditor_asset')->defaultValue('Vivo\AssetBundle\Model\CkeditorAsset')->cannotBeEmpty()->end()
                        ->scalarNode('file')->defaultValue('Vivo\AssetBundle\Model\File')->cannotBeEmpty()->end()
                    ->end()
                ->end()
                ->arrayNode('repository')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('asset')->defaultValue('Vivo\AssetBundle\Repository\AssetRepository')->cannotBeEmpty()->end()
                        ->scalarNode('ckeditor_asset')->defaultValue('Vivo\AssetBundle\Repository\CkeditorAssetRepository')->cannotBeEmpty()->end()
                        ->scalarNode('file')->defaultValue('Vivo\AssetBundle\Repository\FileRepository')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
