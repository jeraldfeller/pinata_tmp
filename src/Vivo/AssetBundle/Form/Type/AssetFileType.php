<?php

namespace Vivo\AssetBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;
use Vivo\AssetBundle\Model\AssetInterface;

/**
 * Class AssetFileType.
 */
class AssetFileType extends AbstractType
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
        $this->fileClass = $fileClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', 'Vivo\UtilBundle\Form\Type\SecureHiddenEntityType', array_merge(array(
                    'error_bubbling' => false,
                    'class' => $this->fileClass,
                    'constraints' => array(
                        new Valid(),
                    ),
                ), $options['file_options'])
            )
            ->add('title', null, array(
                'required' => false,
                'label' => 'form.asset_file.title',
            ))
            ->add('alt', null, array(
                'required' => false,
                'label' => 'form.asset_file.alt',
            ))
            ->add('subtitle', null, array(
                'required' => false,
                'label' => 'form.asset_file.subtitle',
            ))
            ->add('colorclass', 'Vivo\UtilBundle\Form\Type\ColourType', array(
                'required' => false,
                'label' => 'form.asset_file.colorclass',
            ))
            ->add('status', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'form.asset_file.status',
                'value' => 1
            ))
            ->add('rank', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                'error_bubbling' => false,
            ))
        ;

        if ($options['schedulable']) {
            $builder
                ->add('activeAt', 'Vivo\UtilBundle\Form\Type\DateTimePickerType', array(
                    'required' => false,
                    'label' => 'form.asset_file.activeAt',
                ))
                ->add('expiresAt', 'Vivo\UtilBundle\Form\Type\DateTimePickerType', array(
                    'required' => false,
                    'label' => 'form.asset_file.expiresAt',
                ))
            ;
        } else {
            if ($options['clear_schedule']) {
                $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                    $data = $event->getData();

                    if (null === $data) {
                        return;
                    }

                    if ($data instanceof AssetInterface) {
                        $data->setActiveAt(null)
                            ->setExpiresAt(null);

                        $event->setData($data);
                    }
                });
            }
        }

        if ($options['show_filename']) {
            $builder->add('filename', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'form.asset_file.filename',
            ));
        } else {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $data = $event->getData();

                /*
                 * We need to add the filename field as a hidden field
                 * so the uploaded filename can be stored. The filename
                 * will not be editable after the initial upload.
                 */
                $event->getForm()
                    ->add('filename', 'Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
                        'required' => false,
                        'read_only' => null !== $data && $data->getId() ? true : false,
                    ))
                ;
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'file_options' => array(),
            'validation_groups' => array('vivo_asset_asset_file'),
            'show_filename' => true,
            'schedulable' => false,
            'clear_schedule' => true, // Clears the schedule if schedulable is false
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
        return 'vivo_asset_asset_file';
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
