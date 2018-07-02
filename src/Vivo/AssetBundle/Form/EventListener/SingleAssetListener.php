<?php

namespace Vivo\AssetBundle\Form\EventListener;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vivo\AssetBundle\Model\AssetInterface;

class SingleAssetListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => array('preSetData', 50),
            FormEvents::SUBMIT => 'onSubmit',
        );
    }

    public function preSetData(FormEvent $event)
    {
        if (null === $data = $event->getData()) {
            return;
        }

        $event->setData(array($data));
    }

    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (is_array($data) || $data instanceof \Traversable || $data instanceof \ArrayAccess) {
            $rank = null;
            $asset = null;

            foreach ($data as $result) {
                if ($result instanceof AssetInterface) {
                    if (null === $rank || $result->getRank() < $rank) {
                        $asset = $result;
                    }
                }
            }

            if (null !== $asset) {
                $event->setData($asset);

                return;
            }
        }

        $event->setData(null);
    }
}
