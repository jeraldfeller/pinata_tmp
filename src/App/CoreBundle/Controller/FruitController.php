<?php

namespace App\CoreBundle\Controller;

use App\CoreBundle\Entity\Fruit;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\Page;
use Vivo\PageBundle\Model\PageInterface;
use App\CoreBundle\Repository\FruitRepository;

class FruitController extends Controller
{
    public function indexAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date

        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var FruitRepository $fruitRepo */
        $fruitRepo = $this->get('app_core.repository.fruit');

        $fruits = $fruitRepo->findAllForListPage();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        return $this->render('@AppCore/Fruit/index.html.twig', array(
            'page' => $cmsPage,
            'fruits' => $fruits,
        ), $response);
    }

    public function viewAction(Request $request, $slug, PageInterface $cmsPage)
    {
        /** @var FruitRepository $fruitRepo */
        $fruitRepo = $this->get('app_core.repository.fruit');

        /** @var Fruit $fruit */
        $fruit = $fruitRepo->findOneBySlugWithMenuNodes($slug);

        if (!$fruit) {
            return $this->redirect($this->generateUrl('app_core.fruit.index'), 301);
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        $seoPage = $this->get('vivo_site.seo.page');

        $seoPage->addTitlePart($fruit->getName());

        return $this->render('@AppCore/Fruit/view.html.twig', array(
            'page' => $cmsPage,
            'fruit' => $fruit,
        ));
    }

    public function homepageCardsAction(Request $request)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date

        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var FruitRepository $fruitRepo */
        $fruitRepo = $this->get('app_core.repository.fruit');

        $fruits = $fruitRepo->findAllForListPage();

        return $this->render('@AppCore/Fruit/cards.html.twig', array(
            'fruits' => $fruits,
        ), $response);
    }
}
