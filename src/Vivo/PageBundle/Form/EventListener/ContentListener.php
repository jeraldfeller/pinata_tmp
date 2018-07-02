<?php

/**
 * This subscriber customises the content type form.
 */

namespace Vivo\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vivo\PageBundle\Model\ContentInterface;
use Vivo\PageBundle\PageType\Type\PageTypeInterface;

/**
 * Class ContentListener.
 */
class ContentListener implements EventSubscriberInterface
{
    /**
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * @param PageTypeInterface $pageType
     *
     * @return $this
     */
    public function setPageType(PageTypeInterface $pageType)
    {
        $this->pageType = $pageType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data || !$this->pageType) {
            return;
        }
        
        $help_inline = 'admin.form.page.mainContentNote';
        $id = $data->getId();
        
         if($id === 16){    
            $help_inline = 'admin.form.page.promotionalImageNote';
         }
         
        if ($data instanceof ContentInterface) {
            if ($contentBlock = $this->pageType->getContentBlockByAlias($data->getAlias())) {
                $form->add('content', $contentBlock->getFormType(), array_replace_recursive(
                    array(
                        'label' => $contentBlock->getName(),
                        'help_inline' => $help_inline,
                    ),
                    $contentBlock->getOptions()
                ));
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmitData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data || !$this->pageType) {
            return;
        }

        if (isset($data['alias']) && $data['alias']) {
            if ($contentBlock = $this->pageType->getContentBlockByAlias($data['alias'])) {
                $form->add('content', $contentBlock->getFormType(), array_replace_recursive(
                    array(
                        'label' => $contentBlock->getName(),
                    ),
                    $contentBlock->getOptions()
                ));
            }
        }
    }
}
