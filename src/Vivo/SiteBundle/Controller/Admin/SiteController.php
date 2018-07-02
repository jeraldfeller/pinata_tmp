<?php

namespace Vivo\SiteBundle\Controller\Admin;

use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\SiteBundle\Model\SiteInterface;

/**
 * Site Controller.
 */
class SiteController extends Controller
{
    /**
     * Lists all Site entities.
     */
    public function indexAction(Request $request)
    {
        if (!$this->isGranted('ROLE_DEVELOPER')) {
            if ($this->isGranted('ROLE_SITE_MANAGEMENT')) {
                return $this->redirectToRoute('vivo_site.admin.site.edit');
            } else {
                throw new AccessDeniedException();
            }
        }

        $siteRepository = $this->get('vivo_site.repository.site');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $siteRepository
            ->getAllWithDomainsQueryBuilder()
            ->orderBy('site.updatedAt', 'desc');

        $searchList = $this->getSearchList();

        $form = $this->createForm('Vivo\UtilBundle\Form\Type\SearchListType', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoSite/Admin/Site/index.html.twig', array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 25),
            )
        );
    }

    /**
     * Switch user to another site.
     */
    public function switchAction(Request $request, SiteInterface $currentSite, $id, $token)
    {
        /** @var \Vivo\SiteBundle\Model\SiteInterface $site */
        $site = $this->findSiteById($id);

        if (!$site) {
            throw $this->createNotFoundException(sprintf("Unable to find Site entity with id '%s'.", $id));
        }

        if ($token) {
            if (!$this->isGranted('IS_AUTHENTICATED_ANONYMOUSLY')) {
                throw new AccessDeniedException();
            }
        } elseif (!$this->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException();
        }

        /** @var \Symfony\Component\HttpFoundation\Session\SessionInterface $session */
        $session = $this->get('session');
        /** @var \Symfony\Component\Routing\RouterInterface $router */
        $router = $this->get('router');

        $nextUrl = null;

        try {
            $nextUrl = $router->generate($request->query->get('route'), (array) $request->query->get('route_params'));
        } catch (RouteNotFoundException $e) {
        } catch (MissingMandatoryParametersException $e) {
        }

        if ($token) {
            if (true === $this->isCsrfTokenValid($site->getCsrfIntention('switch'), $token)) {
                return $this->redirect($nextUrl ?: $router->generate('admin_homepage'));
            } else {
                $session->invalidate();

                throw new AccessDeniedException();
            }
        } else {
            if (!$nextUrl) {
                if (!$nextUrl = $request->server->get('HTTP_REFERER', false)) {
                    $nextUrl = $this->generateUrl('admin_homepage');
                }
            }

            if ($currentSite === $site) {
                return $this->redirect($nextUrl);
            }

            $primaryDomain = $site->getPrimaryDomain();

            $routerContext = $router->getContext();
            $routerContext->setScheme($primaryDomain->getScheme());
            $routerContext->setHost($primaryDomain->getHost(true));

            /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager */
            $csrfTokenManager = $this->get('security.csrf.token_manager');

            $routeParams = array(
                'id' => $site->getId(),
                'token' => (string) $csrfTokenManager->getToken($site->getCsrfIntention('switch')),
                'sessionId' => $session->getId(),
                'route' => $request->query->get('route'),
                'route_params' => $request->query->get('route_params'),
            );

            return $this->redirect($router->generate($request->attributes->get('_route'), $routeParams, UrlGeneratorInterface::ABSOLUTE_URL));
        }
    }

    /**
     * Creates a new Site entity.
     */
    public function createAction(Request $request)
    {
        if (!$this->isGranted('ROLE_DEVELOPER')) {
            throw new AccessDeniedException();
        }

        /** @var \Vivo\SiteBundle\Repository\SiteRepositoryInterface $siteRepository */
        $siteRepository = $this->container->get('vivo_site.repository.site');
        $site = $siteRepository->createSite();

        $form = $this->createForm('Vivo\SiteBundle\Form\Type\SiteType', $site, array(
            'is_developer' => true,
        ));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($site);
            $em->flush();
            $this->addFlash('success', 'admin.flash.site.create.success');

            return $this->redirectToRoute('vivo_site.admin.site.index');
        }

        return $this->render('@VivoSite/Admin/Site/create.html.twig', array(
            'site' => $site,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Site entity.
     */
    public function updateAction(Request $request, SiteInterface $currentSite, $id = null)
    {
        if (!$this->isGranted('ROLE_SITE_MANAGEMENT') || !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }

        if ($id) {
            if (!$this->isGranted('ROLE_DEVELOPER')) {
                if ($this->isGranted('ROLE_SITE_MANAGEMENT')) {
                    return $this->redirectToRoute('vivo_site.admin.site.edit');
                } else {
                    throw new AccessDeniedException();
                }
            }
        }

        $em = $this->getDoctrine()->getManager();

        /* @var \Vivo\SiteBundle\Model\SiteInterface $site */
        if (null === $id) {
            $site = $currentSite;
        } else {
            $site = $this->findSiteById($id);
        }

        if (!$site) {
            throw $this->createNotFoundException(sprintf("Unable to find Site entity with id '%s'.", $id));
        }

        $isDeveloper = $this->isGranted('ROLE_DEVELOPER') ? true : false;

        $form = $this->createForm('Vivo\SiteBundle\Form\Type\SiteType', $site, array('is_developer' => $isDeveloper));

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($site);
            $em->flush();
            $this->addFlash('success', 'admin.flash.site.update.success');

            return $this->redirectToRoute(
                null === $id ? 'vivo_site.admin.site.edit' : 'vivo_site.admin.site.index'
            );
        }

        return $this->render('@VivoSite/Admin/Site/update.html.twig', array(
            'site' => $site,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Site entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        if (!$this->isGranted('ROLE_DEVELOPER') || !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $site = $this->findSiteById($id);

        if (!$site) {
            throw $this->createNotFoundException('Unable to find Site entity.');
        }

        if (true !== $this->isCsrfTokenValid($site->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            try {
                $em->remove($site);
                $em->flush();
                $this->addFlash('success', 'admin.flash.site.delete.success');
            } catch (DBALException $e) {
                $this->addFlash('error', 'admin.flash.site.delete.failed');
            }
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_site.admin.site.index');
    }

    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'VivoSiteBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }

    /**
     * @param $id
     *
     * @return \Vivo\SiteBundle\Model\SiteInterface
     */
    protected function findSiteById($id)
    {
        return $this->get('vivo_site.repository.site')->findOneByIdWithDomains($id);
    }

    /**
     * @return SearchList
     */
    protected function getSearchList($context = null)
    {
        $searchList = new SearchList();
        $searchList->setEqualColumns(array('site.id'))
            ->setLikeColumns(array('site.name', 'domain.host'));

        return $searchList;
    }
}
