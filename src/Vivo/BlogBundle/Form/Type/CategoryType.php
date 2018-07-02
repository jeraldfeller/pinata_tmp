<?php

namespace Vivo\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    /**
     * @var string
     */
    protected $categoryClass;

    /**
     * @var string
     */
    protected $categorySlugClass;

    /**
     * @param string $categoryClass
     * @param string $categorySlugClass
     */
    public function __construct($categoryClass, $categorySlugClass)
    {
        $this->categoryClass = $categoryClass;
        $this->categorySlugClass = $categorySlugClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $categorySlugReflection = new \ReflectionClass($this->categorySlugClass);

        $builder
            ->add('title', null, array(
                'label' => 'admin.form.category.title',
            ))
            ->add('primarySlug', 'Vivo\SlugBundle\Form\Type\SlugType', array(
                'slug_entity' => $categorySlugReflection->newInstance(),
                'label' => 'admin.form.category.slug',
            ))
            ->add('metaTitle', null, array(
                'required' => false,
                'label' => 'admin.form.category.metaTitle',
            ))
            ->add('metaDescription', null, array(
                'required' => false,
                'label' => 'admin.form.category.metaDescription',
            ))
            ->add('socialTitle', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'admin.form.category.socialTitle',
            ))
            ->add('socialDescription', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'admin.form.category.socialDescription',
            ))
            ->add('robotsNoIndex', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.category.noIndex',
            ))
            ->add('robotsNoFollow', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.category.noFollow',
            ))
            ->add('excludedFromSitemap', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.category.excludedFromSitemap',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->categoryClass,
            'translation_domain' => 'VivoBlogBundle',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_blog_category_type';
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
