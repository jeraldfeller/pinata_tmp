<?php

namespace Vivo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;

class UserUpdateType extends AbstractType
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
            ->add('updatePassword', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'form.user.password.update',
            ))
            ->add('newPassword', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'form.user.password.new',
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
            ->add('group', 'Vivo\AdminBundle\Form\Choice\GroupChoice', array(
                'label' => 'form.user.group',
            ))
            ->add('firstName', null, array(
                'label' => 'form.user.firstName',
            ))
            ->add('lastName', null, array(
                'label' => 'form.user.lastName',
            ))
            ->add('disabledAt', 'Vivo\UtilBundle\Form\Type\CheckboxToDateTimeType', array(
                'label' => 'form.user.disabledAt.label',
                'help_inline' => 'form.user.disabledAt.help',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vivo\AdminBundle\Form\Model\PasswordUpdate',
            'translation_domain' => 'VivoAdminBundle',
            'validation_groups' => function (FormInterface $form) {
                if ($form->getData()->updatePassword) {
                    return array('Default', 'userUpdate', 'updatePassword');
                }

                return array('Default', 'userUpdate');
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_user_update_type';
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
