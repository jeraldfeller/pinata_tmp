<?php

namespace Vivo\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vivo\PageBundle\Model\PageInterface;

/**
 * Class DisabledAtListener.
 */
class DisabledAtListener implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'setData',
            FormEvents::SUBMIT => 'setData',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function setData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if ($data instanceof PageInterface && ($pageType = $data->getPageTypeInstance())) {
            if ($pageType->isAlwaysEnabled() && $data->isDisabled()) {
                $data->setDisabledAt(null);
                $event->setData($data);
            }
        }
    }
}
