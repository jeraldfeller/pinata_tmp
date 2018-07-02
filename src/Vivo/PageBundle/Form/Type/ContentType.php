<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\PageBundle\Form\EventListener\ContentListener;

class ContentType extends AbstractType
{
    /**
     * @var \Vivo\PageBundle\Form\EventListener\ContentListener
     */
    protected $contentListener;

    /**
     * @var string
     */
    protected $contentClass;

    /**
     * Constructor.
     *
     * @param string          $contentClass
     * @param ContentListener $contentListener
     */
    public function __construct($contentClass, ContentListener $contentListener)
    {
        $this->contentClass = $contentClass;
        $this->contentListener = $contentListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('alias', 'Symfony\Component\Form\Extension\Core\Type\HiddenType');

        $this->contentListener->setPageType($options['page_type']);

        $builder->addEventSubscriber($this->contentListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'page_type' => null,
            'data_class' => $this->contentClass,
        ));

        $resolver->setRequired(array(
            'page_type',
        ));

        $resolver->setAllowedTypes('page_type', array('Vivo\PageBundle\PageType\Type\PageTypeInterface'));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_content_type';
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
