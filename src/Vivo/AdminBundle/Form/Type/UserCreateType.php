<?php

namespace Vivo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserCreateType extends AbstractType
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
            ->add('newPassword', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'form.user.password.initial',
            ));
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
            ->add('email', null, array(
                'label' => 'form.user.email',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vivo\AdminBundle\Form\Model\PasswordCreate',
            'validation_groups' => array('Default', 'userCreate'),
            'translation_domain' => 'VivoAdminBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_user_create_type';
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
