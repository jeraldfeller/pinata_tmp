<?php

namespace Vivo\BlogBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    /**
     * @var string
     */
    protected $postClass;

    /**
     * @var string
     */
    protected $postSlugClass;

    /**
     * @var bool
     */
    protected $includeAuthorField;

    /**
     * @param string $postClass
     * @param string $postSlugClass
     * @param bool   $includeAuthorField
     */
    public function __construct($postClass, $postSlugClass, $includeAuthorField)
    {
        $this->postClass = $postClass;
        $this->postSlugClass = $postSlugClass;
        $this->includeAuthorField = $includeAuthorField;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $postSlugReflection = new \ReflectionClass($this->postSlugClass);

        $builder
            ->add('publicationDate', 'Vivo\UtilBundle\Form\Type\DateTimePickerType', array(
                'label' => 'admin.form.post.publicationDate',
            ))
            ->add('title', null, array(
                'label' => 'admin.form.post.title',
            ))
            ->add('excerpt', null, array(
                'label' => 'admin.form.post.excerpt',
            ))
            ->add('body', 'Trsteel\CkeditorBundle\Form\Type\CkeditorType', array(
                'label' => 'admin.form.post.body',
            ))
            ->add('disabledAt', 'Vivo\UtilBundle\Form\Type\CheckboxToDateTimeType', array(
                'inverted' => true,
                'label' => 'admin.form.post.disabledAt.label',
                'required' => false,
                'help_inline' => 'admin.form.post.disabledAt.help',
            ))
            ->add('primarySlug', 'Vivo\SlugBundle\Form\Type\SlugType', array(
                'slug_entity' => $postSlugReflection->newInstance(),
                'label' => 'admin.form.post.slug',
            ))
            ->add('metaTitle', null, array(
                'required' => false,
                'label' => 'admin.form.post.metaTitle',
            ))
            ->add('metaDescription', null, array(
                'required' => false,
                'label' => 'admin.form.post.metaDescription',
            ))
            ->add('socialTitle', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'admin.form.post.socialTitle',
            ))
            ->add('socialDescription', 'Symfony\Component\Form\Extension\Core\Type\TextType', array(
                'required' => false,
                'label' => 'admin.form.post.socialDescription',
            ))
            ->add('robotsNoIndex', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.post.noIndex',
            ))
            ->add('robotsNoFollow', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.post.noFollow',
            ))
            ->add('excludedFromSitemap', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
                'required' => false,
                'label' => 'admin.form.post.excludedFromSitemap',
            ))
            ->add('categories', 'Vivo\BlogBundle\Form\Choice\CategoryChoice', array(
                'label' => 'admin.form.post.categories',
            ))
        ;

        if ($this->includeAuthorField) {
            $builder->add('author', null, array(
                'label' => 'admin.form.post.author',
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $validationGroups = array('Default');

        if ($this->includeAuthorField) {
            $validationGroups[] = 'DefaultWithAuthor';
        }

        $resolver->setDefaults(array(
            'data_class' => $this->postClass,
            'translation_domain' => 'VivoBlogBundle',
            'validation_groups' => $validationGroups,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_blog_post_type';
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
