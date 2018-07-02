<?php

namespace Vivo\PageBundle\Form\Choice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vivo\PageBundle\Form\DataTransformer\PageTypeInstanceToAliasTransformer;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;

class PageTypeChoice extends AbstractType
{
    protected $pageTypeManager;
    protected $choices;

    /**
     * @param PageTypeManagerInterface $pageTypeManager
     */
    public function __construct(PageTypeManagerInterface $pageTypeManager)
    {
        $this->pageTypeManager = $pageTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new PageTypeInstanceToAliasTransformer($this->pageTypeManager);

        $builder->addModelTransformer($transformer);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => true,
            'placeholder' => '-- Select a page type',
            'current_page' => null,
            'choices' => function (Options $options) {
                return $this->getChoices($options['current_page']);
            },
        ));

        $resolver->setAllowedTypes('current_page', array('null', 'Vivo\PageBundle\Model\PageInterface'));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\ChoiceType';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'vivo_page_choice_page_type';
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

    /**
     * @return array
     */
    protected function getChoices(PageInterface $currentPage = null)
    {
        if (null === $this->choices) {
            $this->choices = [];
            foreach ($this->pageTypeManager->getPageTypes() as $type) {
                $this->choices[$type->getAlias()] = $type->getName();
            }

            if ($currentPage instanceof PageInterface && null !== $currentPage->getPageTypeInstance()) {
                if (!array_key_exists($currentPage->getPageTypeInstance()->getAlias(), $this->choices)) {
                    $this->choices[$currentPage->getPageTypeInstance()->getAlias()] = $currentPage->getPageTypeInstance()->getName();
                }
            }

            asort($this->choices);
        }

        return $this->choices;
    }
}
