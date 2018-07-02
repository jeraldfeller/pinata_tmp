<?php

namespace Vivo\TreeBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class MergeCollectionListener.
 *
 * Merges the original collection with the
 * choices that were not selected
 */
class MergeCollectionListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    protected $originalChoices;

    /**
     * @param array $originalChoices
     */
    public function __construct(array $originalChoices)
    {
        $this->originalChoices = $originalChoices;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onSubmit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function onSubmit(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        $originalCollection = $event->getForm()->getNormData();

        foreach ($originalCollection as $key => $value) {
            if (false !== array_key_exists($value, $this->originalChoices) && false === in_array($value, $data)) {
                unset($originalCollection[$key]);
            }
        }

        $event->setData(array_merge($originalCollection, $data));
    }
}
