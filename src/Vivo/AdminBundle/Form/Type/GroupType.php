<?php

namespace Vivo\AdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    /**
     * @var string
     */
    protected $groupClass;

    /**
     * @param string $groupClass
     */
    public function __construct($groupClass)
    {
        $this->groupClass = $groupClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => 'form.group.name',
            ))
            ->add('roles', 'Vivo\AdminBundle\Form\Choice\RolesChoice', array(
                'label' => 'form.group.roles',
            ))
            ->add('selfManaged', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => 'form.group.selfManaged.label',
                'help_inline' => 'form.group.selfManaged.help',
                'required' => false,
            ))
        ;

        if ($options['is_developer']) {
            $builder->add('alias', null, array(
                'label' => 'form.group.alias',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'VivoAdminBundle',
            'data_class' => $this->groupClass,
            'is_developer' => false,
        ));

        $resolver->setAllowedTypes('is_developer', array('bool'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_admin_group_type';
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
