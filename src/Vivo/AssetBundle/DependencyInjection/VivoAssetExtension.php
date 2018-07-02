<?php

namespace Vivo\AssetBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VivoAssetExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        if ($container->hasExtension($vivoUtil = 'vivo_util')) {
            $container->prependExtensionConfig($vivoUtil, array(
                'mapped_superclasses' => array(
                    array(
                        'model' => $config['model']['ckeditor_asset'],
                        'repository' => $config['repository']['ckeditor_asset'],
                    ),
                    array(
                        'model' => $config['model']['file'],
                        'repository' => $config['repository']['file'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($doctrineAlias = 'doctrine')) {
            $container->prependExtensionConfig($doctrineAlias, array(
                'orm' => array(
                    'resolve_target_entities' => array(
                        'Vivo\AssetBundle\Model\AssetInterface' => $config['model']['asset'],
                        'Vivo\AssetBundle\Model\FileInterface' => $config['model']['file'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($liipImagineAlias = 'liip_imagine')) {
            $container->prependExtensionConfig($liipImagineAlias, array(
                'driver' => 'imagick',
                'data_loader' => 'vivo_asset_file_data_loader',
                'filter_sets' => array(
                    'content' => array(
                        'data_loader' => 'vivo_asset_file_data_loader',
                        'quality' => 90,
                        'filters' => array(
                            'strip' => array(),
                            'thumbnail' => array('size' => array(800, 800), 'mode' => 'inset'),
                        ),
                    ),
                    'asset' => array(
                        'data_loader' => 'vivo_asset_file_data_loader',
                        'quality' => 90,
                        'filters' => array(
                            'strip' => array(),
                            'thumbnail' => array('size' => array(1200, 1200), 'mode' => 'inset'),
                        ),
                    ),
                    'asset_collection_thumb' => array(
                        'data_loader' => 'vivo_asset_file_data_loader',
                        'quality' => 70,
                        'filters' => array(
                            'strip' => array(),
                            'thumbnail' => array('size' => array(270, 185), 'mode' => 'inset'),
                            'padding' => array('size' => array(270, 185), 'background' => '#f6f6f6'),
                        ),
                    ),
                    'asset_collection_icon' => array(
                        'data_loader' => 'default',
                        'cache' => 'default',
                        'quality' => 70,
                        'filters' => array(
                            'strip' => array(),
                            'thumbnail' => array('size' => array(270, 185), 'mode' => 'inset'),
                            'padding' => array('size' => array(270, 185), 'background' => '#f6f6f6'),
                        ),
                    ),
                    'file_browser_thumb' => array(
                        'data_loader' => 'vivo_asset_file_data_loader',
                        'quality' => 70,
                        'filters' => array(
                            'strip' => array(),
                            'thumbnail' => array('size' => array(80, 10000), 'mode' => 'inset'),
                        ),
                    ),
                    'file_browser_icon' => array(
                        'data_loader' => 'default',
                        'cache' => 'default',
                        'quality' => 70,
                        'filters' => array(
                            'strip' => array(),
                            'thumbnail' => array('size' => array(80, 10000), 'mode' => 'inset'),
                        ),
                    ),
                ),
            ));
        }

        if ($container->hasExtension($trsteelCkeditorAlias = 'trsteel_ckeditor')) {
            $container->prependExtensionConfig($trsteelCkeditorAlias, array(
                'filebrowser_browse_url' => array('route' => 'vivo_asset.ckeditor_browser.index', 'route_parameters' => array('type' => 'file')),
                'filebrowser_image_browse_url' => array('route' => 'vivo_asset.ckeditor_browser.index', 'route_parameters' => array('type' => 'image')),
                'filebrowser_upload_url' => array('route' => 'vivo_asset.ckeditor_browser.quick_upload'),
                'filebrowser_image_upload_url' => array('route' => 'vivo_asset.ckeditor_browser.quick_upload', 'route_parameters' => array('type' => 'image')),
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // @TODO: check_download_context should be removed from v4.0 (@deprecated)
        $container->setParameter('vivo_asset.check_download_context', $config['check_download_context']);
        $container->setParameter('vivo_asset.create_non_existent_images', $config['create_non_existent_images']);
        $container->setParameter('vivo_asset.upload_directory', rtrim($config['upload_directory'], '/').'/');
        $container->setParameter('vivo_asset.model.asset', $config['model']['asset']);
        $container->setParameter('vivo_asset.model.ckeditor_asset', $config['model']['ckeditor_asset']);
        $container->setParameter('vivo_asset.model.file', $config['model']['file']);

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array(
                '@VivoAsset/Form/vivo_asset_asset_file_collection.html.twig',
                '@VivoAsset/Form/vivo_asset_asset_file.html.twig',
            )
        ));

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0

            $bcFormTypes = array(
                'vivo_asset.form.file' => 'vivo_asset_file',
                'vivo_asset.form.asset_upload' => 'vivo_asset_asset_upload',
                'vivo_asset.form.asset_file_collection' => 'vivo_asset_asset_file_collection',
                'vivo_asset.form.asset_image_collection' => 'vivo_asset_asset_image_collection',
                'vivo_asset.form.asset_file' => 'vivo_asset_asset_file',
                'vivo_asset.form.asset_image' => 'vivo_asset_asset_image',
                'vivo_asset.form.asset_file_basic' => 'vivo_asset_asset_file_basic',
            );

            foreach ($bcFormTypes as $name => $alias) {
                $container->getDefinition($name)
                    ->clearTag('form.type')
                    ->addTag('form.type', array('alias' => $alias));
            }
        }
    }
}
