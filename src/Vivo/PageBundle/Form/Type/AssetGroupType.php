<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\PageBundle\Form\EventListener\AssetGroupListener;

class AssetGroupType extends AbstractType
{
    /**
     * @var string
     */
    protected $assetGroupClass;

    /**
     * @var \Vivo\PageBundle\Form\EventListener\AssetGroupListener
     */
    protected $assetGroupListener;

    /**
     * Constructor.
     *
     * @param string             $assetGroupClass
     * @param AssetGroupListener $assetGroupListener
     */
    public function __construct($assetGroupClass, AssetGroupListener $assetGroupListener)
    {
        $this->assetGroupClass = $assetGroupClass;
        $this->assetGroupListener = $assetGroupListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alias', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');

        $this->assetGroupListener->setPageType($options['page_type']);

        $builder->addEventSubscriber($this->assetGroupListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'page_type' => null,
            'data_class' => $this->assetGroupClass,
        ));

        $resolver->setRequired(array(
            'page_type',
        ));

        $resolver->setAllowedTypes('page_type', array('Vivo\PageBundle\PageType\Type\PageTypeInterface'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_asset_group_type';
    }

    /**
     * BC - Add alias if Symfony < 3.0.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
