<?php

namespace App\CoreBundle\Form\Type\Admin;

use App\CoreBundle\Entity\Farm;
use App\CoreBundle\Model\Choice\FruitChoice;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FarmType extends AbstractType
{
    /**
     * @var string
     */
    protected $farmSlugClass;

    /**
     * @param string $farmSlugClass
     */
    public function __construct($farmSlugClass)
    {
        $this->farmSlugClass = $farmSlugClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $farmSlugReflection = new \ReflectionClass($this->farmSlugClass);

        $builder
            ->add('introduction', 'textarea', array(
                'required' => false,
            ))
            ->add('disabledAt', 'vivo_util_checkbox_datetime', array(
                'inverted' => true,
                'label' => 'Active',
                'required' => false,
                'help_inline' => 'Tick to enable farm',
            ))
            ->add('name', null, array(
                'label' => 'Name',
                'help_inline' => 'Page title on detail page eg. Bowen Mangoes',
            ))
            ->add('locationName', null, array(
                'label' => 'Name',
                'help_inline' => 'Name in list and on Map eg. Bowen',
            ))
            ->add('content', 'ckeditor', array(
                'label' => 'Content',
            ))
            ->add('headOffice', 'checkbox', array(
                'label' => 'Head Office Location',
                'required' => false,
            ))
            ->add('thirdPartyFarm', 'checkbox', array(
                'label' => 'Third Party Farm',
                'required' => false,
            ))
            ->add('fruits', 'choice', array(
                'choices' => FruitChoice::$choices,
                'expanded' => true,
                'multiple' => true,
            ))
            ->add('address', 'vivo_address_map', array(
                'inherit_data' => true,
            ))
            ->add('image', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Map Thumbnail: 340x180px',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\CoreBundle\Entity\FarmImage',
                ),
            ))
            ->add('contentImage', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Content Image: 1220x670px (without caption) or 910x500px (with caption)',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\CoreBundle\Entity\FarmImage',
                ),
            ))
            ->add('bannerImage', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Banner Image - Edge to Edge above introduction',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\CoreBundle\Entity\FarmImage',
                ),
            ))
            ->add('primarySlug', 'vivo_slug', array(
                'slug_entity' => $farmSlugReflection->newInstance(),
                'label' => 'URL',
                'help_block' => 'eg /farms/<strong>url</strong> The /farms part is not required',
            ))
            ->add('promoGroup', 'entity', array(
                'required' => false,
                'empty_value' => 'None',
                'class' => 'AppCoreBundle:PromoBlockGroup',
                'property' => 'name',
                'label' => 'Promo Block',
                'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('b')
                            ->orderBy('b.name', 'ASC')
                            ->addOrderBy('b.id', 'ASC');
                    },
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'data_class' => 'App\CoreBundle\Entity\Farm',
            'translation_domain' => 'AppCoreBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_core_farm_type';
    }
}
