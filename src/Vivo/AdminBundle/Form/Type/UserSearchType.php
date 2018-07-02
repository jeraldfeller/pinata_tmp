<?php

namespace Vivo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('group', 'Vivo\AdminBundle\Form\Choice\GroupChoice', array(
                'placeholder' => 'Any',
                'required' => false,
                'label' => 'Group',
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
            'data_class' => 'Vivo\AdminBundle\Form\Model\UserSearch',
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
        return 'vivo_admin_user_search';
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
