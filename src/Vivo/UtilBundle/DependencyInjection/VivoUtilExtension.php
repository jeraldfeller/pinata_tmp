<?php

namespace Vivo\UtilBundle\DependencyInjection;

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
class VivoUtilExtension extends Extension
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

        $container->setParameter('vivo_util.mapped_superclasses', isset($config['mapped_superclasses']) ? $config['mapped_superclasses'] : array());

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array(
                '@VivoUtil/Form/vivo_util_colour.html.twig',
                '@VivoUtil/Form/vivo_util_datetime.html.twig',
                '@VivoUtil/Form/vivo_util_seo.html.twig',
                '@VivoUtil/Form/search_list.html.twig',
            )
        ));

        $container->setParameter('vivo_util.geoip.city_database', $config['geoip']['city_database']);
        $container->setParameter('vivo_util.geoip.country_database', $config['geoip']['country_database']);
        $container->setParameter('vivo_util.geoip.cache_dir', $config['geoip']['cache_dir']);

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0

            $bcFormTypes = array(
                'vivo_util.form.vivo_util_colour' => 'vivo_util_colour',
                'vivo_util.form.seo_meta' => 'vivo_util_seo',
                'vivo_util.form.checkbox_datetime' => 'vivo_util_checkbox_datetime',
                'vivo_util.form.vivo_util_secure_hidden_entity' => 'vivo_util_secure_hidden_entity',
                'vivo_util.form.vivo_util_date' => 'vivo_util_date',
                'vivo_util.form.vivo_util_time' => 'vivo_util_time',
                'vivo_util.form.vivo_util_datetime' => 'vivo_util_datetime',
                'vivo_util.form.search_list' => 'search_list',
            );

            foreach ($bcFormTypes as $name => $alias) {
                $container->getDefinition($name)
                    ->clearTag('form.type')
                    ->addTag('form.type', array('alias' => $alias));
            }
        }
    }
}
