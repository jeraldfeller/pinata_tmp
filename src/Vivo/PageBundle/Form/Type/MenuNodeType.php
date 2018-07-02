<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuNodeType extends AbstractType
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
        $builder->add('parent', 'Vivo\TreeBundle\Form\Choice\TreeChoice', array(
            'property' => 'title',
            'class' => $this->menuNodeClass,
        ))
        ->add('title', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
            'required' => false,
        ))
        ->add('remove', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'attr' => array(
                'class' => 'collection-remove',
            ),
            'mapped' => false,
            'required' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->menuNodeClass,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_menu_node_type';
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
