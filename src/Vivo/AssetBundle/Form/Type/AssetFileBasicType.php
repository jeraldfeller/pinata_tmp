<?php

namespace Vivo\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @deprecated Deprecated since version 3.2, to be removed in 4.0
 */
class AssetFileBasicType extends AbstractType
{
    /**
     * @var string
     */
    protected $fileClass;

    /**
     * @param $fileClass
     */
    public function __construct($fileClass)
    {
        trigger_error('Deprecated since version 3.2 and will be removed in 4.0.', E_USER_DEPRECATED);

        $this->fileClass = $fileClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'vivo_util_secure_hidden_entity', array_merge(array(
                    'error_bubbling' => false,
                    'class' => $this->fileClass,
                ), $options['file_options'])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'file_options' => array(),
            'validation_groups' => array('vivo_asset_asset_file'),
            'translation_domain' => 'VivoAssetBundle',
        ));

        $resolver->setRequired(array(
            'data_class',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_asset_asset_file_basic';
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
