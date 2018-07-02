<?php

namespace App\CoreBundle\Form\Type\Admin;

use App\CoreBundle\Model\Choice\ColorClassChoice;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FruitType extends AbstractType
{
    /**
     * @var string
     */
    protected $fruitSlugClass;

    /**
     * @param string $fruitSlugClass
     */
    public function __construct($fruitSlugClass)
    {
        $this->fruitSlugClass = $fruitSlugClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $fruitSlugReflection = new \ReflectionClass($this->fruitSlugClass);

        $builder
            ->add('disabledAt', 'vivo_util_checkbox_datetime', array(
                'inverted' => true,
                'label' => 'Active',
                'required' => false,
                'help_inline' => 'Tick to enable fruit',
            ))
            ->add('name')
            ->add('subtitle')
            ->add('intro', 'textarea', array(
                'label' => 'Homepage Card Intro',
                'help_inline' => 'For homepage card - Max 255 characters',
            ))
            ->add('pageIntroduction', 'textarea', array(
                'label' => 'Page Introduction',
            ))
            ->add('content', 'ckeditor', array(
                'label' => 'Content',
                'help_inline' => '<p class="help-inline alert">1 column full width images upload at: 1220px wide (615px wide if a caption or sidebar is enabled). <br>2 column images upload at: 560x373px.<br>3 Podcast images only: 350x270px.</p>',
            ))
            ->add('colorClass', 'choice', array(
                'choices' => ColorClassChoice::$choices,
            ))
            ->add('primaryImage', 'vivo_asset_asset_image_collection', array(
                'label' => 'Main Card Image: 360x210px',
                'multiple' => false,
                'options' => array(
                    'data_class' => 'App\CoreBundle\Entity\FruitImage',
                ),
            ))
            ->add('contentImage', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Content Image: 1220x670px (without caption) or 910x500px (with caption)',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\CoreBundle\Entity\FruitImage',
                ),
            ))
            ->add('bannerImage', 'vivo_asset_asset_image_collection', array(
                'label' => 'Banner Image',
                'multiple' => false,
                'options' => array(
                    'data_class' => 'App\CoreBundle\Entity\FruitImage',
                ),
            ))
            ->add('primarySlug', 'vivo_slug', array(
                'slug_entity' => $fruitSlugReflection->newInstance(),
                'label' => 'URL',
                'help_block' => 'eg /fruits/<strong>url</strong> The /fruits part is not required',
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
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\CoreBundle\Entity\Fruit',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_corebundle_fruit';
    }
}
