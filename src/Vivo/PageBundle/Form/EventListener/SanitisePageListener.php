<?php

/**
 * This subscriber sanitises the data before it is displayed.
 */

namespace Vivo\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vivo\PageBundle\Form\Model\PageModel;

class SanitisePageListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'sanitiseData',
            FormEvents::POST_SUBMIT => 'sanitiseData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function sanitiseData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof PageModel) {
            if ($pageType = $data->getPageTypeInstance()) {
                $data->setPage($pageType->getSanitisedPage());
                $event->setData($data);
            }
        }
    }
}
