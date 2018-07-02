<?php

namespace App\FaqBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FaqType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'entity', array(
                'empty_value' => 'Select a category',
                'class' => 'AppFaqBundle:Category',
                'property' => 'title',
                'label' => 'admin.form.faq.category',
            ))
            ->add('question', null, array(
                'label' => 'admin.form.faq.question',
            ))
            ->add('answer', 'ckeditor', array(
                'label' => 'admin.form.faq.answer',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\FaqBundle\Entity\Faq',
            'translation_domain' => 'AppFaqBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_faq_admin_faq_type';
    }
}
