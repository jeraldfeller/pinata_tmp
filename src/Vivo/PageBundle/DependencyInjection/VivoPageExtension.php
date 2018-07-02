<?php

namespace Vivo\PageBundle\DependencyInjection;

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
class VivoPageExtension extends Extension implements PrependExtensionInterface
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
                        'model' => $config['model']['asset'],
                        'repository' => $config['repository']['asset'],
                    ),
                    array(
                        'model' => $config['model']['asset_group'],
                        'repository' => $config['repository']['asset_group'],
                    ),
                    array(
                        'model' => $config['model']['content'],
                        'repository' => $config['repository']['content'],
                    ),
                    array(
                        'model' => $config['model']['menu_node'],
                        'repository' => $config['repository']['menu_node'],
                    ),
                    array(
                        'model' => $config['model']['page'],
                        'repository' => $config['repository']['page'],
                    ),
                    array(
                        'model' => $config['model']['slug'],
                        'repository' => $config['repository']['slug'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($doctrineAlias = 'doctrine')) {
            $container->prependExtensionConfig($doctrineAlias, array(
                'orm' => array(
                    'resolve_target_entities' => array(
                        'Vivo\PageBundle\Model\AssetInterface' => $config['model']['asset'],
                        'Vivo\PageBundle\Model\AssetGroupInterface' => $config['model']['asset_group'],
                        'Vivo\PageBundle\Model\ContentInterface' => $config['model']['content'],
                        'Vivo\PageBundle\Model\MenuNodeInterface' => $config['model']['menu_node'],
                        'Vivo\PageBundle\Model\PageInterface' => $config['model']['page'],
                        'Vivo\PageBundle\Model\SlugInterface' => $config['model']['slug'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($cmfRoutingAlias = 'cmf_routing')) {
            $container->prependExtensionConfig($cmfRoutingAlias, array(
                'chain' => array(
                    'routers_by_id' => array(
                        'cmf_routing.dynamic_router' => 20,
                        'router.default' => 100,
                    ),
                ),
                'dynamic' => array(
                    'enabled' => true,
                    'route_provider_service_id' => 'vivo_page.routing.provider',
                ),
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

        foreach ($config['model'] as $type => $class) {
            $container->setParameter(sprintf('vivo_page.model.%s', $type), $class);
        }

        foreach ($config['page_type'] as $type => $value) {
            $container->setParameter(sprintf('vivo_page.page_type.%s.enabled', $type), $value['enabled']);
            $container->setParameter(sprintf('vivo_page.page_type.%s.class', $type), $value['class']);

            if (isset($value['blocks'])) {
                foreach ($value['blocks'] as $blockKey => $blocks) {
                    $container->setParameter(sprintf('vivo_page.page_type.%s.blocks.%s', $type, $blockKey), $blocks);
                }
            }
        }

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0

            $bcFormTypes = array(
                'vivo_page.form.choice.page_type' => 'vivo_page_choice_page_type',
                'vivo_page.form.page_type' => 'vivo_page_page_type',
                'vivo_page.form.base_page_type' => 'vivo_page_base_page_type',
                'vivo_page.form.homepage_page_type' => 'vivo_page_homepage_page_type',
                'vivo_page.form.placeholder_page_type' => 'vivo_page_placeholder_page_type',
                'vivo_page.form.page_menu_type' => 'vivo_page_menu_type',
                'vivo_page.form.page_menu_node_type' => 'vivo_page_menu_node_type',
                'vivo_page.form.page_content_type' => 'vivo_page_content_type',
                'vivo_page.form.page_asset_group_type' => 'vivo_page_asset_group_type',
            );

            foreach ($bcFormTypes as $name => $alias) {
                $container->getDefinition($name)
                    ->clearTag('form.type')
                    ->addTag('form.type', array('alias' => $alias));
            }
        }
    }
}
