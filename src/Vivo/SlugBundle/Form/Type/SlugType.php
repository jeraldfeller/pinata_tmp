<?php

namespace Vivo\SlugBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\SiteBundle\Factory\SiteFactory;
use Vivo\SlugBundle\Form\DataTransformer\SlugToStringTransformer;

/**
 * SlugType.
 */
class SlugType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var SiteFactory
     */
    protected $siteFactory;

    /**
     * @param ObjectManager $objectManager
     * @param SiteInterface
     */
    public function __construct(ObjectManager $objectManager, SiteFactory $siteFactory)
    {
        $this->objectManager = $objectManager;
        $this->siteFactory = $siteFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new SlugToStringTransformer($this->objectManager, $options['slug_entity'], $options['allow_slashes'], $options['site'], $options['reserved_slugs']));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
            'slug_entity' => null,
            'allow_slashes' => false,
            'site' => $this->siteFactory->get(),
            'reserved_slugs' => array(),
            'invalid_message' => 'Sorry, this value is reserved.',
        ));

        $resolver->setRequired(array(
            'slug_entity',
        ));

        $resolver->setAllowedTypes('site', array('Vivo\SiteBundle\Model\SiteInterface'));
        $resolver->setAllowedTypes('slug_entity', array('Vivo\SlugBundle\Model\SlugInterface'));
        $resolver->setAllowedTypes('allow_slashes', array('bool'));
        $resolver->setAllowedTypes('reserved_slugs', array('array'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\TextType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_slug';
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
