<?php

/**
 * This subscriber sanitises the data before it is displayed.
 */

namespace Vivo\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vivo\PageBundle\Form\Model\PageModel;

class PageTypeInstanceListener implements EventSubscriberInterface
{
    protected $isEditable = false;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::POST_SET_DATA => 'postSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof PageModel) {
            if (!$data->getPageTypeInstance() || $data->getPageTypeInstance()->isPageTypeChangable()) {
                $this->isEditable = true;
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function postSetData(FormEvent $event)
    {
        $data = $event->getData();

        $event->getForm()
            ->add('pageTypeInstance', 'Vivo\PageBundle\Form\Choice\PageTypeChoice', array(
                'label' => 'admin.form.page.type',
                'disabled' => !$this->isEditable,
                'current_page' => $data instanceof PageModel ? $data->getPage() : null,
            ))
        ;
    }
}
