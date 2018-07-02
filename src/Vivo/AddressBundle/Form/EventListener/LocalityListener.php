<?php

namespace Vivo\AddressBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Vivo\AddressBundle\Model\LocalityInterface;
use Vivo\AddressBundle\Repository\SuburbRepositoryInterface;

class LocalityListener implements EventSubscriberInterface
{
    /**
     * @var \Vivo\AddressBundle\Repository\SuburbRepositoryInterface
     */
    protected $suburbRepository;

    /**
     * @var array
     */
    protected $suburbOptions;

    /**
     * @param SuburbRepositoryInterface $suburbRepository
     * @param array                     $suburbOptions
     */
    public function __construct(SuburbRepositoryInterface $suburbRepository, array $suburbOptions)
    {
        $this->suburbRepository = $suburbRepository;
        $this->suburbOptions = $suburbOptions;
    }

    /*
    * {@inheritdoc}
    */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::POST_SUBMIT => 'postSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof LocalityInterface) {
            $this->customiseForm($event->getForm(), $data->getCountryCode(), $data->getPostcode());

            if ($data instanceof LocalityInterface) {
                switch ($data->getCountryCode()) {
                    case 'AU':
                        if ($data->getPostcode() && $data->getSuburb()) {
                            if ($suburb = $this->suburbRepository->findOneByPostcodeAndSuburb($data->getCountryCode(), $data->getPostcode(), $data->getSuburb())) {
                                $data->setState($suburb->getState());
                                $event->setData($data);
                            }
                        }

                        break;
                }
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        $this->customiseForm($event->getForm(), $data['countryCode'], $data['postcode']);
    }

    /**
     * @param FormEvent $event
     */
    public function postSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof LocalityInterface) {
            switch ($data->getCountryCode()) {
                case 'AU':
                    if ($data->getPostcode() && $data->getSuburb()) {
                        if ($suburb = $this->suburbRepository->findOneByPostcodeAndSuburb($data->getCountryCode(), $data->getPostcode(), $data->getSuburb())) {
                            $data->setState($suburb->getState());
                            $event->setData($data);
                        }
                    }

                    break;
            }
        }
    }

    /**
     * @param FormInterface $form
     * @param $country
     * @param $postcode
     */
    protected function customiseForm(FormInterface $form, $country, $postcode)
    {
        switch ($country) {
            case 'AU':
                $suburbOptions = $this->suburbOptions;

                if (!isset($suburbOptions['label'])) {
                    $suburbOptions['label'] = 'form.address.suburb';
                }

                $form
                    ->add('postcode', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                        'label' => 'form.address.postcode',
                    ))
                    ->add('suburb', 'Vivo\AddressBundle\Form\Choice\SuburbChoice', array_merge($suburbOptions, array(
                        'country' => $country,
                        'postcode' => $postcode,
                    )))
                    ->add('state', 'Symfony\Component\Form\Extension\Core\Type\HiddenType')
                ;

                break;

            default:
                $form
                    ->add('postcode', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                        'label' => 'form.address.postcode',
                    ))
                    ->add('suburb', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                        'label' => 'form.address.suburb',
                    ))
                    ->add('state', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                        'label' => 'form.address.state',
                    ))
                ;

                break;
        }
    }
}
