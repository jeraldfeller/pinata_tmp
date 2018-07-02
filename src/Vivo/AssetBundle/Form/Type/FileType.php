<?php

namespace Vivo\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\AssetBundle\Factory\FileFactoryInterface;
use Vivo\AssetBundle\Form\DataTransformer\FileDataTransformer;

class FileType extends AbstractType
{
    /**
     * @var FileFactoryInterface
     */
    protected $fileFactory;

    /**
     * @var string
     */
    protected $fileClass;

    /**
     * @param FileFactoryInterface $fileFactory
     * @param $fileClass
     */
    public function __construct(FileFactoryInterface $fileFactory, $fileClass)
    {
        $this->fileFactory = $fileFactory;
        $this->fileClass = $fileClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new FileDataTransformer($this->fileFactory);

        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => false,
            'data_class' => $this->fileClass,
            'invalid_message' => 'Could not read file. Uploaded file may be too large.',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\FileType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_asset_file';
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
