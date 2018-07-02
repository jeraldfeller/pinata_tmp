<?php

namespace Vivo\AdminBundle\DependencyInjection;

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
class VivoAdminExtension extends Extension implements PrependExtensionInterface
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
                        'model' => $config['model']['group'],
                        'repository' => $config['repository']['group'],
                    ),
                    array(
                        'model' => $config['model']['user'],
                        'repository' => $config['repository']['user'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($doctrineAlias = 'doctrine')) {
            $container->prependExtensionConfig($doctrineAlias, array(
                'orm' => array(
                    'resolve_target_entities' => array(
                        'Vivo\AdminBundle\Model\UserInterface' => $config['model']['user'],
                        'Vivo\AdminBundle\Model\GroupInterface' => $config['model']['group'],
                    ),
                ),
            ));
        }

        if ($container->hasExtension($knpPaginatorAlias = 'knp_paginator')) {
            $container->prependExtensionConfig($knpPaginatorAlias, array(
                'page_range' => 9,
                'template' => array(
                    'pagination' => '@VivoAdmin/Pagination/sliding.html.twig',
                    'sortable' => '@VivoAdmin/Pagination/sortable_link.html.twig',
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
            array('@VivoAdmin/Form/search_list_widget.html.twig')
        ));

        $container->setParameter('vivo_admin.firewall_name', $config['firewall_name']);

        foreach ($config['model'] as $type => $class) {
            $container->setParameter(sprintf('vivo_admin.model.%s', $type), $class);
        }

        $container->setParameter('vivo_admin.reset_ttl', $config['reset_ttl']);

        $container->setParameter('vivo_admin.password_expiry.enabled', $config['password_expiry']['enabled']);
        $container->setParameter('vivo_admin.password_expiry.route', $config['password_expiry']['route']);
        $container->setParameter('vivo_admin.password_expiry.target_query_parameter', $config['password_expiry']['target_query_parameter']);
        $container->setParameter('vivo_admin.password_expiry.csrf_query_parameter', $config['password_expiry']['csrf_query_parameter']);

        $container->setParameter('vivo_admin.roles', $config['roles']);

        $container->setParameter('vivo_admin.menu.class', $config['menu_class']);

        if (Kernel::VERSION_ID < 30000) {
            // BC - Add alias if Symfony < 3.0

            $bcFormTypes = array(
                'vivo_admin.form.group' => 'vivo_admin_group_type',
                'vivo_admin.form.user_create' => 'vivo_admin_user_create_type',
                'vivo_admin.form.user_update' => 'vivo_admin_user_update_type',
                'vivo_admin.form.user_search' => 'vivo_admin_user_search',
                'vivo_admin.form.group_search' => 'vivo_admin_group_search',
                'vivo_admin.form.profile' => 'vivo_admin_profile_type',
                'vivo_admin.form.password_reset' => 'vivo_admin_password_type',
                'vivo_admin.form.password_expired' => 'vivo_admin_password_expired_type',
                'vivo_admin.form.group_choice' => 'vivo_admin_group_choice_type',
                'vivo_admin.form.roles_choice' => 'vivo_admin_roles_choice',
            );

            foreach ($bcFormTypes as $name => $alias) {
                $container->getDefinition($name)
                    ->clearTag('form.type')
                    ->addTag('form.type', array('alias' => $alias));
            }
        }
    }
}
