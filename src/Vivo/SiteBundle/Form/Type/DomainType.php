<?php

namespace Vivo\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DomainType extends AbstractType
{
    /**
     * @var string
     */
    protected $domainClass;

    /**
     * @param string $domainClass
     */
    public function __construct($domainClass)
    {
        $this->domainClass = $domainClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('host', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'label' => 'admin.form.domain.host',
            ))
            ->add('primary', 'Symfony\Component\Form\Extension\Core\Type\RadioType', array(
                'required' => false,
                'label' => 'admin.form.domain.primary',
            ))
            ->add('wwwSubdomain', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.domain.wwwSubdomain',
            ))
            ->add('secure', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.domain.secure',
            ))
            ->add('remove', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => 'admin.form.domain.remove',
                'mapped' => false,
                'required' => false,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->domainClass,
            'translation_domain' => 'VivoSiteBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_site_domain_type';
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
