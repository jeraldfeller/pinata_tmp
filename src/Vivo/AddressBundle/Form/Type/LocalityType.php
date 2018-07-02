<?php

namespace Vivo\AddressBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\AddressBundle\Form\EventListener\LocalityListener;
use Vivo\AddressBundle\Repository\SuburbRepositoryInterface;

class LocalityType extends AbstractType
{
    /**
     * @var \Vivo\AddressBundle\Repository\SuburbRepositoryInterface
     */
    protected $suburbRepository;

    /**
     * @param SuburbRepositoryInterface $suburbRepository
     */
    public function __construct(SuburbRepositoryInterface $suburbRepository)
    {
        $this->suburbRepository = $suburbRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('countryCode', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
            'read_only' => true,
        ));

        $suburbOptions = array(
            'select_suburb_label' => $options['select_suburb_label'],
            'empty_postcode_label' => $options['empty_postcode_label'],
            'invalid_postcode_label' => $options['invalid_postcode_label'],
        );

        $suburbOptions = array_filter($suburbOptions, function ($option) {
            return null === $option ? false : true;
        });

        $builder->addEventSubscriber(new LocalityListener($this->suburbRepository, $suburbOptions));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'select_suburb_label' => null,
            'empty_postcode_label' => null,
            'invalid_postcode_label' => null,
            'data_class' => 'Vivo\AddressBundle\Model\Locality',
        ));

        $resolver->setAllowedTypes('select_suburb_label', array('null', 'string'));
        $resolver->setAllowedTypes('empty_postcode_label', array('null', 'string'));
        $resolver->setAllowedTypes('invalid_postcode_label', array('null', 'string'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_address_locality';
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
