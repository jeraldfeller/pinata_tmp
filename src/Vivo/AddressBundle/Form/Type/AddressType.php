<?php

namespace Vivo\AddressBundle\Form\Type;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Vivo\AddressBundle\Model\AddressInterface;
use Vivo\AddressBundle\Model\ExtendedAddressInterface;
use Vivo\AddressBundle\Model\LocalityInterface;

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (true === $options['include_country']) {
            $builder->add('countryCode', 'Symfony\Component\Form\Extension\Core\Type\CountryType', array(
                'placeholder' => 'Select Country',
                'label' => 'form.address.countryCode',
            ));
        }

        if (true === $options['include_company']) {
            $builder->add('company', null, array(
                'label' => 'form.address.company',
            ));
        }

        $builder
            ->add('addressLine1', null, array(
                'label' => 'form.address.addressLine1',
            ))
            ->add('addressLine2', null, array(
                'label' => 'form.address.addressLine2',
            ))
            ->add('locality', 'Vivo\AddressBundle\Form\Type\LocalityType', array_merge(
                array(
                    'constraints' => array(new Valid()),
                ),
                $options['locality_options']
            ))
        ;

        if (false !== $options['ignore_label']) {
            $builder->add('ignored', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'label' => $options['ignore_label'],
            ));
        }

        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'setCountry'));
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'setCountry'));
    }

    /**
     * @param FormEvent $event
     */
    public function setCountry(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof LocalityInterface) {
            $options = $event->getForm()->getConfig()->getOptions();

            if ($options['default_country'] && (!$options['include_country'] || null === $data->getCountryCode())) {
                $data->setCountryCode($options['default_country']);
                $event->setData($data);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'locality_options' => array(),
            'include_country' => false,
            'default_country' => 'AU',
            'include_company' => false,
            'ignore_label' => false,
            'translation_domain' => 'VivoAddressBundle',
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if ($data instanceof ExtendedAddressInterface) {
                    if (true === $data->isIgnored()) {
                        return false;
                    }
                }

                if ($data instanceof AddressInterface) {
                    return array('DefaultAddress');
                }

                return;
            },
        ));

        $resolver->setRequired(array(
            'data_class',
        ));

        $resolver->setAllowedValues('default_country', array_keys(Intl::getRegionBundle()->getCountryNames()));

        $resolver->setAllowedTypes('locality_options', array('array'));
        $resolver->setAllowedTypes('include_country', array('bool'));
        $resolver->setAllowedTypes('default_country', array('null', 'string'));
        $resolver->setAllowedTypes('include_company', array('bool'));
        $resolver->setAllowedTypes('ignore_label', array('bool', 'string'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_address';
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
