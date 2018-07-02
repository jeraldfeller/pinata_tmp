<?php

namespace Vivo\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\PageBundle\PageType\Type\PlaceholderPageType;

/**
 * Page controller.
 */
class PageController extends Controller
{
    /**
     * Permanent action.
     */
    public function permanentAction(Request $request, $id)
    {
        /** @var \Vivo\PageBundle\Repository\PageRepositoryInterface $pageRepository */
        $pageRepository = $this->get('vivo_page.repository.page');
        $page = $pageRepository->findOnePageByIdWithAllJoins($id);

        if ($page) {
            if ($page->getPageTypeInstance() instanceof PlaceholderPageType) {
                return $this->placeholderAction($request, $page);
            }

            if (null !== $routeName = $page->getPageTypeInstance()->getRouteName()) {
                return $this->redirectToRoute($routeName);
            }
        }

        throw $this->createNotFoundException(sprintf("Could not find page for id '%s'", $id));
    }

    /**
     * Redirects placeholder to first child page.
     *
     * This is necessary incase a normal page becomes a placeholder
     */
    public function placeholderAction(Request $request, PageInterface $cmsPage)
    {
        /** @var \Vivo\PageBundle\Repository\MenuNodeRepositoryInterface $menuNodeRepository */
        $menuNodeRepository = $this->get('vivo_page.repository.menu_node');

        $primaryTrail = $menuNodeRepository->getPrimaryParentTrailOf($cmsPage, false);

        if ($primaryMenuNode = end($primaryTrail)) {
            return $this->redirectToRoute($primaryMenuNode->getRouteName(), $request->query->all(), Response::HTTP_MOVED_PERMANENTLY);
        }

        throw $this->createNotFoundException(sprintf("Could not find child page for placeholder id '%s'", $cmsPage->getId()));
    }

    /**
     * View Page Action.
     */
    public function viewAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date
        if ($response->isNotModified($request)) {
            return $response;
        }
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));
        return $this->render('@VivoPage/Page/view.html.twig', array(
            'page' => $cmsPage,
        ), $response);


    }

    /**
     * Homepage Action.
     */
    public function homepageAction(Request $request, PageInterface $cmsPage = null)
    {
        if ($cmsPage && !$cmsPage->isDisabled()) {
            $response = new Response();
            $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date
            if ($response->isNotModified($request)) {
                return $response;
            }

            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $this->container->get('event_dispatcher');
            $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

            return $this->render('@VivoPage/Page/homepage.html.twig', array(
                'page' => $cmsPage,
            ), $response);
        }

        /** @var \Vivo\PageBundle\Repository\PageRepositoryInterface $pageRepository */
        $pageRepository = $this->get('vivo_page.repository.page');
        $page = $pageRepository->findFirstPage();

        if ($page) {
            return $this->forward($page->getPageTypeInstance()->getController(), array(
                '_route' => $page->getPageTypeInstance()->getRouteName(),
                'cmsPage' => $page,
            ));
        }

        throw new ServiceUnavailableHttpException(86400, 'Homepage could not be found.');
    }

    /**
     * Service Unavailable.
     */
    public function unavailableAction()
    {
        throw new ServiceUnavailableHttpException(86400);
    }

    /**
     * This redirects all old Page slugs to their primary slug.
     */
    public function catchAllAction(Request $request, PageInterface $cmsPage, $uri)
    {
        $router = $this->get('router');

        try {
            $route = $router->match('/' . trim($cmsPage->getPageTypeInstance()->getSlug(), '/') . '/' . $uri);
            $url = $this->generateUrl($route['_route'], array_merge($request->query->all(), $route));
        } catch (RouteNotFoundException $e) {
            throw $this->createNotFoundException();
        }

        return $this->redirect($url, Response::HTTP_MOVED_PERMANENTLY);
    }

    /**
     * Submit job action
     */
    public function submitJobAction()
    {
        $data = $_POST;
        $get = $_GET['param'];
        $file = fopen("../tmp/test.txt", "a");
        fwrite($file, json_encode($get));
        fclose($file);

    }
}
