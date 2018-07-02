<?php

/**
 * This listener customises the form.
 */

namespace Vivo\PageBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Vivo\PageBundle\Model\AssetGroupInterface;
use Vivo\PageBundle\PageType\Type\PageTypeInterface;

/**
 * Class AssetGroupListener.
 */
class AssetGroupListener implements EventSubscriberInterface
{
    /**
     * @var string
     */
    protected $assetClass;

    /**
     * @var PageTypeInterface
     */
    protected $pageType;

    /**
     * Constructor.
     *
     * @param string $assetClass
     */
    public function __construct($assetClass)
    {
        $this->assetClass = $assetClass;
    }

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

        if ($data instanceof AssetGroupInterface) {
            if ($assetGroupBlock = $this->pageType->getAssetGroupBlockByAlias($data->getAlias())) {
                $form->add('assets', $assetGroupBlock->getFormType(), array_replace_recursive(array(
                    'label' => $assetGroupBlock->getName(),
                    'entry_options' => array(
                        'data_class' => $this->assetClass,
                    ),
                    'constraints' => array(
                        new Valid(),
                    ),
                ), $assetGroupBlock->getOptions()));
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
            if ($assetGroupBlock = $this->pageType->getAssetGroupBlockByAlias($data['alias'])) {
                $form->add('assets', $assetGroupBlock->getFormType(), array_replace_recursive(array(
                    'label' => $assetGroupBlock->getName(),
                    'entry_options' => array(
                        'data_class' => $this->assetClass,
                    ),
                ), $assetGroupBlock->getOptions()));
            }
        }
    }
}
