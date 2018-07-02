<?php

namespace App\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'Name',
            ))
            ->add('email', 'email', array(
                'label' => 'Email',
            ))
            ->add('subject', null, array(
                'label' => 'Subject',
            ))
            ->add('message', 'textarea', array(
                'label' => 'Message',
            ))
            ->add('submit', 'submit', array(
                'attr' => array(
                    'class' => 'form-submit',
                ),
            ))
        ;
    }

    public function getName()
    {
        return 'app_core_contact_type';
    }
}
