<?php

namespace Vivo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordExpiredType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currentPassword', 'Symfony\Component\Form\Extension\Core\Type\PasswordType', array(
                'label' => 'form.passwordExpired.password.current',
            ))
            ->add('newPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'options' => array('translation_domain' => 'VivoAdminBundle'),
                'first_options' => array('label' => 'form.passwordExpired.password.new'),
                'second_options' => array('label' => 'form.passwordExpired.password.confirm'),
                'invalid_message' => 'vivo_admin.password.mismatch',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vivo\AdminBundle\Form\Model\PasswordChange',
            'validation_groups' => array('Default', 'changePassword', 'expiredPassword'),
            'translation_domain' => 'VivoAdminBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_password_expired_type';
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
