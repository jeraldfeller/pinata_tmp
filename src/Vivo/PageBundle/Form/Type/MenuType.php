<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    /**
     * @var string
     */
    protected $menuNodeClass;

    /**
     * @param string $menuNodeClass
     */
    public function __construct($menuNodeClass)
    {
        $this->menuNodeClass = $menuNodeClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('alias', null, array(
                'label' => 'admin.form.menu.alias',
            ))
            ->add('title', null, array(
                'label' => 'admin.form.menu.title',
            ))
            ->add('primary', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.menu.primary',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->menuNodeClass,
            'translation_domain' => 'VivoPageBundle',
            'validation_groups' => array('menu'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_menu_type';
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
