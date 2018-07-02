<?php

namespace Vivo\PageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\PageBundle\Form\EventListener\PageTypeInstanceListener;
use Vivo\PageBundle\Form\Model\PageModel;
use Vivo\PageBundle\Form\EventListener\PageTypeListener;
use Vivo\PageBundle\Form\EventListener\SanitisePageListener;

class PageType extends AbstractType
{
    /**
     * @var \Vivo\PageBundle\Form\EventListener\PageTypeInstanceListener
     */
    protected $pageTypeInstanceListener;

    /**
     * @var \Vivo\PageBundle\Form\EventListener\PageTypeListener
     */
    protected $pageTypeListener;

    /**
     * @var \Vivo\PageBundle\Form\EventListener\SanitisePageListener
     */
    protected $pageSanitiserListener;

    /**
     * @param PageTypeInstanceListener $pageTypeInstanceListener
     * @param PageTypeListener         $pageTypeListener
     * @param SanitisePageListener     $pageSanitiserListener
     */
    public function __construct(PageTypeInstanceListener $pageTypeInstanceListener, PageTypeListener $pageTypeListener, SanitisePageListener $pageSanitiserListener)
    {
        $this->pageTypeInstanceListener = $pageTypeInstanceListener;
        $this->pageTypeListener = $pageTypeListener;
        $this->pageSanitiserListener = $pageSanitiserListener;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('softPost', 'Symfony\Component\Form\Extension\Core\Type\SubmitType', array(
            'validation_groups' => false,
        ));

        $builder->addEventSubscriber($this->pageTypeInstanceListener);
        $builder->addEventSubscriber($this->pageSanitiserListener);
        $builder->addEventSubscriber($this->pageTypeListener);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'translation_domain' => 'VivoPageBundle',
            'data_class' => 'Vivo\PageBundle\Form\Model\PageModel',
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if ($data instanceof PageModel) {
                    if ($pageType = $data->getPageTypeInstance()) {
                        return array_merge(array('page_model'), $pageType->getValidationGroups());
                    }
                }

                return array('page_model', 'Default');
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_page_type';
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
