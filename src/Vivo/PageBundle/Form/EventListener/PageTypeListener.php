<?php

/**
 * This subscriber adds the additional PageType form.
 */

namespace Vivo\PageBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Vivo\PageBundle\Form\Model\PageModel;
use Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface;

class PageTypeListener implements EventSubscriberInterface
{
    /**
     * @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface
     */
    protected $pageTypeManager;

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
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmitData',
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
            if ($pageType = $data->getPageTypeInstance()) {
                if ($formType = $data->getPageTypeInstance()->getFormType()) {
                    $form = $event->getForm();

                    $form->add('page', $formType, array(
                        'page_type' => $pageType,
                        'constraints' => array(new Valid()),
                    ));
                }
            }
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmitData(FormEvent $event)
    {
        $data = $event->getData();

        if (null === $data) {
            return;
        }

        if (isset($data['pageTypeInstance']) && $data['pageTypeInstance']) {
            if ($pageType = $this->pageTypeManager->getPageTypeInstanceByAlias($data['pageTypeInstance'])) {
                if ($formType = $pageType->getFormType()) {
                    $form = $event->getForm();

                    $form->add('page', $formType, array(
                        'page_type' => $pageType,
                        'constraints' => array(new Valid()),
                    ));
                }
            }
        }
    }
}
