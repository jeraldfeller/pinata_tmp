<?php

namespace Vivo\TwitterBootstrapBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VivoTwitterBootstrapExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        if ($container->hasExtension($mopaBootstrapAlias = 'mopa_bootstrap')) {
            $container->prependExtensionConfig($mopaBootstrapAlias, array(
                'form' => array(
                    'render_fieldset' => false,
                    'render_collection_item' => false,
                    'show_legend' => false,
                    'show_child_legend' => false,
                    'checkbox_label' => 'label',
                    'render_optional_text' => false,
                    'render_required_asterisk' => false,
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
    }
}
