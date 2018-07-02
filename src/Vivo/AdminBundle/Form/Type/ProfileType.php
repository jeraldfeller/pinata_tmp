<?php

namespace Vivo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Valid;

class ProfileType extends AbstractType
{
    /**
     * @var string
     */
    protected $userClass;

    /**
     * @param string $userClass
     */
    public function __construct($userClass)
    {
        $this->userClass = $userClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $child = $builder->create('user', 'Symfony\Component\Form\Extension\Core\Type\FormType', array(
            'data_class' => $this->userClass,
            'translation_domain' => 'VivoAdminBundle',
            'constraints' => array(
                new Valid(),
            ),
        ));

        $this->buildUserForm($child, $options);

        $builder
            ->add($child)
            ->add('changePassword', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'form.profile.password.change',
            ))
            ->add('currentPassword', 'Symfony\Component\Form\Extension\Core\Type\PasswordType', array(
                'required' => false,
                'label' => 'form.profile.password.current',
            ))
            ->add('newPassword', 'Symfony\Component\Form\Extension\Core\Type\RepeatedType', array(
                'required' => false,
                'type' => 'Symfony\Component\Form\Extension\Core\Type\PasswordType',
                'invalid_message' => 'vivo_admin.password.mismatch',
                'options' => array('translation_domain' => 'VivoAdminBundle'),
                'first_options' => array('label' => 'form.profile.password.new'),
                'second_options' => array('label' => 'form.profile.password.confirm'),
            ))
        ;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', null, array(
                'label' => 'form.user.firstName',
            ))
            ->add('lastName', null, array(
                'label' => 'form.user.lastName',
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
            'translation_domain' => 'VivoAdminBundle',
            'validation_groups' => function (FormInterface $form) {
                if ($form->getData()->changePassword) {
                    return array('Default', 'changePassword', 'updateProfile');
                }

                return array('Default');
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_profile_type';
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
