<?php

namespace Vivo\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'Vivo\BlogBundle\Form\Choice\CategoryChoice', array(
                'placeholder' => 'Any',
                'required' => false,
                'label' => 'Category',
                'multiple' => false,
                'expanded' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vivo\BlogBundle\Form\Model\PostSearch',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Vivo\UtilBundle\Form\Type\SearchListType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_blog_post_search';
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
