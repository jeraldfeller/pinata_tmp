<?php

namespace Vivo\SiteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\Kernel;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VivoSiteExtension extends Extension implements PrependExtensionInterface
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
                        'model' => $config['model']['site'],
                        'repository' => $config['repository']['site'],
                    ),
                    array(
                        'model' => $config['model']['domain'],
                        'repository' => $config['repository']['domain'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($doctrineAlias = 'doctrine')) {
            $container->prependExtensionConfig($doctrineAlias, array(
                'orm' => array(
                    'resolve_target_entities' => array(
                        'Vivo\SiteBundle\Model\SiteInterface' => $config['model']['site'],
                        'Vivo\SiteBundle\Model\DomainInterface' => $config['model']['domain'],
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

        $container->setParameter('twig.form.resources', array_merge(
            $container->getParameter('twig.form.resources'),
            array(
                '@VivoSite/Form/vivo_site_domain_type.html.twig',
            )
        ));

        $container->setParameter('vivo_site.fallback_to_primary_site', $config['fallback_to_primary_site']);
        $container->setParameter('vivo_site.live_environments', $config['live_environments']);

        $config['site_password']['passwords'][] = $config['site_password']['root_password'];
        $container->setParameter('vivo_site.site_password.enabled', $config['site_password']['enabled']);
        $container->setParameter('vivo_site.site_password.passwords', array_unique(array_filter($config['site_password']['passwords'])));
        $container->setParameter('vivo_site.site_password.route', $config['site_password']['route']);
        $container->setParameter('vivo_site.site_password.target_query_parameter', $config['site_password']['target_query_parameter']);
        $container->setParameter('vivo_site.site_password.csrf_query_parameter', $config['site_password']['csrf_query_parameter']);
        $container->setParameter('vivo_site.site_password.salt', $config['site_password']['salt']);

        foreach ($config['model'] as $type => $class) {
            $container->setParameter(sprintf('vivo_site.model.%s', $type), $class);
        }

        $container->setParameter('vivo_site.google.enabled', $config['google']['enabled']);

        foreach ($config['mailers'] as $mailerId => $mailer) {
            $container->getDefinition('vivo_site.util.mailer')->addMethodCall('addMailer', array($mailerId, $mailer['name'], new Reference($mailer['mailer_id'])));
        }

        $container->setParameter('vivo_site.devices.tablet', $config['devices']['tablet']);
        $container->setParameter('vivo_site.devices.mobile', $config['devices']['mobile']);

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0

            $bcFormTypes = array(
                'vivo_site.form.site' => 'vivo_site_site_type',
                'vivo_site.form.site_domain' => 'vivo_site_domain_type',
                'vivo_site.form.choice.status' => 'vivo_site_status_choice',
            );

            foreach ($bcFormTypes as $name => $alias) {
                $container->getDefinition($name)
                    ->clearTag('form.type')
                    ->addTag('form.type', array('alias' => $alias));
            }
        }
    }
}
