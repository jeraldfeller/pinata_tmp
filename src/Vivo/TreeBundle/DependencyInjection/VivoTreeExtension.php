<?php

namespace Vivo\TreeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VivoTreeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array(
                '@VivoTree/Form/vivo_tree_tree_choice.html.twig',
            )
        ));

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0
            $container->getDefinition('vivo_tree.form.choice.tree')
                ->clearTag('form.type')
                ->addTag('form.type', array('alias' => 'vivo_tree_tree_choice'));
        }
    }
}
