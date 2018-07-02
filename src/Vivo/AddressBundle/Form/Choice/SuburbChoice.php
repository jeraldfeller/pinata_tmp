<?php

namespace Vivo\AddressBundle\Form\Choice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Intl\Intl;
use Vivo\AddressBundle\Repository\SuburbRepositoryInterface;

class SuburbChoice extends AbstractType
{
    /**
     * @var SuburbRepositoryInterface
     */
    protected $suburbRepository;

    /**
     * Constructor.
     *
     * @param SuburbRepositoryInterface $objectManager
     */
    public function __construct(SuburbRepositoryInterface $suburbRepository)
    {
        $this->suburbRepository = $suburbRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['select_suburb_label'] = $options['select_suburb_label'];
        $view->vars['empty_postcode_label'] = $options['empty_postcode_label'];
        $view->vars['invalid_postcode_label'] = $options['invalid_postcode_label'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $suburbRepository = $this->suburbRepository;

        $choices = function (Options $options) use ($suburbRepository) {
            $results = $suburbRepository->findAllByPostcode($options['country'], $options['postcode']);

            $choices = array();
            foreach ($results as $result) {
                $choices[$result->getName()] = $result->getNameAndState();
            }

            return $choices;
        };

        $emptyValue = function (Options $options) use ($suburbRepository) {
            $postcode = (int) $options['postcode'];
            if (!$postcode) {
                return $options['empty_postcode_label'];
            } elseif (count($suburbRepository->findAllByPostcode($options['country'], $options['postcode'])) < 1) {
                return $options['invalid_postcode_label'];
            }

            return $options['select_suburb_label'];
        };

        $resolver->setDefaults(array(
            'choice_translation_domain' => false,
            'select_suburb_label' => 'Please select a suburb...',
            'empty_postcode_label' => 'Please enter a postcode...',
            'invalid_postcode_label' => 'Please enter a valid postcode...',
            'placeholder' => $emptyValue,
            'choices' => $choices,
        ));

        $resolver->setRequired(array(
            'country', 'postcode', 'select_suburb_label', 'empty_postcode_label', 'invalid_postcode_label',
        ));

        $resolver->setAllowedValues('country', array_keys(Intl::getRegionBundle()->getCountryNames()));

        $resolver->setAllowedTypes('country', array('string'));
        $resolver->setAllowedTypes('select_suburb_label', array('string'));
        $resolver->setAllowedTypes('empty_postcode_label', array('string'));
        $resolver->setAllowedTypes('invalid_postcode_label', array('string'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_address_suburb_choice';
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
