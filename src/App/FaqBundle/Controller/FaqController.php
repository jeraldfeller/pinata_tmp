<?php

namespace App\FaqBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Event\PrepareSeoEvent;

/**
 * Faq controller.
 */
class FaqController extends Controller
{
    /**
     * Lists all Faq entities.
     */
    public function indexAction(Request $request, PageInterface $cmsPage)
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');

        /** @var \App\FaqBundle\Repository\CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Category');

        $categories = $categoryRepository->findCategoriesWithFaqsQueryBuilder(true);

        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        return $this->render('@AppFaq/Faq/index.html.twig', array(
            'page' => $cmsPage,
            'categories' => $categories,
        ));
    }
}
