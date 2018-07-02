<?php

namespace Vivo\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\AssetBundle\Model\AssetInterface;

/**
 * Class AssetImageType.
 */
class AssetImageType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['linkable']) {
            $builder
                ->add('link', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                    'required' => false,
                    'label' => 'form.asset_image.link',
                ))
                ->add('linkTarget', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
                    'required' => false,
                    'label' => 'form.asset_image.linkTarget',
                    'choices' => array(
                        '_self' => 'Same Window (_self)',
                        '_blank' => 'New Window (_blank)',
                        '_parent' => 'Parent Window (_parent)',
                    ),
                ))
            ;
        } else {
            if ($options['clear_links']) {
                $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                    $data = $event->getData();

                    if (null === $data) {
                        return;
                    }

                    if ($data instanceof AssetInterface) {
                        $data->setLink(null)
                            ->setLinkTarget(null);

                        $event->setData($data);
                    }
                });
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('vivo_asset_asset_image'),
            'linkable' => false,
            'clear_links' => true, // Clears the link/linkTarget if linkable is false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Vivo\AssetBundle\Form\Type\AssetFileType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_asset_asset_image';
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
