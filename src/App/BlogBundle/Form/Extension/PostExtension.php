<?php

namespace App\BlogBundle\Form\Extension;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

class PostExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('body')
            ->add('featured', 'checkbox', array(
                'label' => 'Featured',
                'help_inline' => 'Feature on homepage (only latest 3 will display)',
                'required' => false,
            ))
            ->add('videoIcon', 'checkbox', array(
                'label' => 'Show Video Icon',
                'required' => false,
            ))
            ->add('introduction', 'textarea', array(
                'required' => false,
            ))
            ->add('body', 'ckeditor', array(
                    'label' => 'admin.form.post.body',
                ))
            ->add('author', 'text', array(
                'label' => 'Author',
            ))
            ->add('image', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Chatter thumbnail image: 615x390',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\BlogBundle\Entity\PostImage',
                ),
            ))
            ->add('contentImage', 'vivo_asset_asset_image_collection', array(
                'multiple' => false,
                'label' => 'Page Hero Image - Under Introduction: 1220x670px (without caption) or 910x500px (with caption)',
                'button_text' => 'Upload Image',
                'options' => array(
                    'data_class' => 'App\BlogBundle\Entity\PostImage',
                ),
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
    public function getExtendedType()
    {
        return 'Vivo\BlogBundle\Form\Type\PostType';
    }
}
