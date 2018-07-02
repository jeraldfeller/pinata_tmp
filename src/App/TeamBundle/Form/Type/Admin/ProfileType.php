<?php

namespace App\TeamBundle\Form\Type\Admin;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    /**
     * @var string
     */
    protected $profileSlugClass;

    /**
     * @param string $profileSlugClass
     */
    public function __construct($profileSlugClass)
    {
        $this->profileSlugClass = $profileSlugClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $profileSlugClass = new \ReflectionClass($this->profileSlugClass);
        $builder
            ->add('disabledAt', 'vivo_util_checkbox_datetime', array(
                'label' => 'admin.form.profile.disabledAt.label',
                'help_inline' => 'admin.form.profile.disabledAt.help',
            ))
            ->add('quote', 'checkbox', array(
                'label' => 'Quote box',
                'required' => false,
                'help_inline' => 'Will display as a quote rather than a team member profile',
            ))
            ->add('name', null, array(
                'label' => 'admin.form.profile.name',
            ))
            ->add('position', null, array(
                'label' => 'admin.form.profile.position',
            ))
            ->add('introduction', 'textarea', array(
                'required' => true,
            ))
            ->add('description', 'ckeditor', array(
                'label' => 'admin.form.profile.description',
            ))
            ->add('image', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Profile Thumbnail: 280x230px',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\TeamBundle\Entity\ProfileImage',
                ),
            ))
            ->add('contentImage', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Content Image: 1220x670px (without caption) or 910x500px (with caption)',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\TeamBundle\Entity\ProfileImage',
                ),
            ))
            ->add('bannerImage', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Banner Image - Edge to Edge above introduction',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\TeamBundle\Entity\ProfileImage',
                ),
            ))
            ->add('primarySlug', 'vivo_slug', array(
                'slug_entity' => $profileSlugClass->newInstance(),
                'label' => 'URL',
                'help_block' => 'eg /our-team/<strong>url</strong> The /our-team part is not required',
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
            'data_class' => 'App\TeamBundle\Entity\Profile',
            'translation_domain' => 'AppTeamBundle',
            'cascade_validation' => true,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'app_team_admin_profile_type';
    }
}
