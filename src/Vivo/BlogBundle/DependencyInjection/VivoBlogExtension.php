<?php

namespace Vivo\BlogBundle\DependencyInjection;

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
class VivoBlogExtension extends Extension implements PrependExtensionInterface
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
                        'model' => $config['model']['category'],
                        'repository' => $config['repository']['category'],
                    ),
                    array(
                        'model' => $config['model']['category_slug'],
                        'repository' => $config['repository']['category_slug'],
                    ),
                    array(
                        'model' => $config['model']['post'],
                        'repository' => $config['repository']['post'],
                    ),
                    array(
                        'model' => $config['model']['post_slug'],
                        'repository' => $config['repository']['post_slug'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($doctrineAlias = 'doctrine')) {
            $container->prependExtensionConfig($doctrineAlias, array(
                'orm' => array(
                    'resolve_target_entities' => array(
                        'Vivo\BlogBundle\Model\CategoryInterface' => $config['model']['category'],
                        'Vivo\BlogBundle\Model\CategorySlugInterface' => $config['model']['category_slug'],
                        'Vivo\BlogBundle\Model\PostInterface' => $config['model']['post'],
                        'Vivo\BlogBundle\Model\PostSlugInterface' => $config['model']['post_slug'],
                    ),
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

        $container->setParameter('vivo_blog.include_author_field', $config['include_author_field']);

        foreach ($config['model'] as $type => $class) {
            $container->setParameter(sprintf('vivo_blog.model.%s', $type), $class);
        }

        foreach ($config['page_type'] as $type => $value) {
            $container->setParameter(sprintf('vivo_blog.page_type.%s.class', $type), $value['class']);

            foreach ($value['blocks'] as $blockKey => $blocks) {
                $container->setParameter(sprintf('vivo_blog.page_type.%s.blocks.%s', $type, $blockKey), $blocks);
            }
        }

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0

            $bcFormTypes = array(
                'vivo_blog.form.post' => 'vivo_blog_post_type',
                'vivo_blog.form.post_search' => 'vivo_blog_post_search',
                'vivo_blog.form.category' => 'vivo_blog_category_type',
                'vivo_blog.form.category_search' => 'vivo_blog_category_search',
                'vivo_blog.form.choice.category' => 'vivo_blog_category_choice',
            );

            foreach ($bcFormTypes as $name => $alias) {
                $container->getDefinition($name)
                    ->clearTag('form.type')
                    ->addTag('form.type', array('alias' => $alias));
            }
        }
    }
}
