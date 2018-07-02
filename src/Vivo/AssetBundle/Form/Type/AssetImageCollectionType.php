<?php

namespace Vivo\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AssetImageCollectionType.
 */
class AssetImageCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'entry_type' => 'Vivo\AssetBundle\Form\Type\AssetImageType',
            'mime_types' => array('image/*'),
            'button_text' => function (Options $options) {
                return 'Upload Image'.(true === $options['multiple'] ? 's' : '');
            },
            'empty_text' => 'No images have been uploaded.',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Vivo\AssetBundle\Form\Type\AssetFileCollectionType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_asset_asset_image_collection';
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
