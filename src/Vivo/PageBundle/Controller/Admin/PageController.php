<?php

namespace Vivo\PageBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Vivo\UtilBundle\Form\Model\SearchList;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\PageBundle\Form\Model\PageModel;

/**
 * Page controller.
 */
class PageController extends BaseController
{
    /**
     * Lists all Page entities.
     */
    public function indexAction(Request $request)
    {
        /** @var \Vivo\PageBundle\Repository\PageRepositoryInterface $pageRepository */
        $pageRepository = $this->get('vivo_page.repository.page');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $pageRepository->createQueryBuilder('page')
            ->leftJoin('page.primarySlug', 'slug')
            ->leftJoin('page.content', 'content')
            ->orderBy('page.updatedAt', 'desc');

        $searchList = $this->getSearchList();

        $form = $this->createForm('Vivo\UtilBundle\Form\Type\SearchListType', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoPage/Admin/Page/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new Page entity.
     */
    public function createAction(Request $request)
    {
        /** @var \Vivo\PageBundle\Repository\PageRepositoryInterface $pageRepository */
        $pageRepository = $this->get('vivo_page.repository.page');
        $page = $pageRepository->createPage();

        if (!$request->isMethod('POST')) {
            /** @var \Vivo\PageBundle\PageType\Manager\PageTypeManagerInterface $pageTypeManager */
            $pageTypeManager = $this->get('vivo_page.manager.page_type');

            if ($pageTypeManager->getDefault()) {
                $page->setPageTypeInstance($pageTypeManager->getDefault());
            }
        }

        $form = $this->createForm('Vivo\PageBundle\Form\Type\PageType', new PageModel($page));
        $form->handleRequest($request);

        foreach ($page->getMenuNodes() as $node) {
            if ($node->getParent()) {
                $node->getParent()->addChild($node);
            }
        }

        if ($form->isSubmitted()) {
            if ($form->get('softPost')->isClicked()) {
                // Regenerate the form so the errors are hidden
                $form = $this->createForm('Vivo\PageBundle\Form\Type\PageType', $form->getData());
            } else {
                if ($form->isValid()) {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($page);
                    $em->flush();
                    $this->addFlash('success', 'admin.flash.page.create.success');

                    return $this->redirectToRoute('vivo_page.admin.tree.index');
                }
            }
        }

        return $this->render('@VivoPage/Admin/Page/create.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Page entity.
     */
    public function updateAction(Request $request, $id)
    {
        $pageRepository = $this->get('vivo_page.repository.page');
        /** @var \Vivo\PageBundle\Model\PageInterface $page */
        $page = $pageRepository->findOnePageByIdWithAllJoins($id);

        if (!$page) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        $originalMenusNodes = array();
        $originalAssetGroups = array();

        if ($request->isMethod('POST')) {
            foreach ($page->getMenuNodes() as $menuNode) {
                $originalMenusNodes[] = $menuNode;
            }

            foreach ($page->getAssetGroups() as $assetGroup) {
                $originalAssetGroups[] = $assetGroup;
            }
        } else {
            if (!$page->getPrimarySlug()) {
                $page->setPrimarySlug($page->getPrimarySlug(true));
            }
        }

        $pageModel = new PageModel($page);

        $form = $this->createForm('Vivo\PageBundle\Form\Type\PageType', $pageModel);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('softPost')->isClicked()) {
                // Regenerate the form so the errors are hidden
                $form = $this->createForm('Vivo\PageBundle\Form\Type\PageType', $form->getData());
            } else {
                if ($form->isValid()) {
                    // filter $originalMenusNodes to contain content no longer present
                    foreach ($page->getMenuNodes() as $node) {
                        foreach ($originalMenusNodes as $key => $toDel) {
                            if ($toDel->getId() === $node->getId()) {
                                unset($originalMenusNodes[$key]);
                            }
                        }
                    }

                    // filter $originalAssetGroups to contain content no longer present
                    foreach ($page->getAssetGroups() as $assetGroup) {
                        foreach ($originalAssetGroups as $key => $toDel) {
                            if ($toDel->getId() === $assetGroup->getId()) {
                                unset($originalAssetGroups[$key]);
                            }
                        }
                    }

                    $em = $this->getDoctrine()->getManager();

                    foreach ($originalMenusNodes as $node) {
                        $page->removeMenuNode($node);
                        $this->get('vivo_page.repository.menu_node')->removeFromTree($node);
                    }

                    foreach ($page->getMenuNodes() as $node) {
                        if ($node->getParent()) {
                            $node->getParent()->addChild($node);
                        }
                    }

                    foreach ($originalAssetGroups as $assetGroup) {
                        $em->remove($assetGroup);
                    }

                    $em->persist($page);
                    $em->flush();

                    $this->addFlash('success', 'admin.flash.page.update.success');

                    return $this->redirectToRoute('vivo_page.admin.tree.index');
                }
            }
        }

        return $this->render('@VivoPage/Admin/Page/update.html.twig', array(
            'page' => $page,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Page entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        /** @var \Vivo\PageBundle\Repository\PageRepositoryInterface $pageRepository */
        $pageRepository = $this->get('vivo_page.repository.page');
        $page = $pageRepository->findOnePageByIdWithAllJoins($id);

        if (!$page || ($page->getPageTypeInstance() && $page->getPageTypeInstance()->isAlwaysEnabled())) {
            throw $this->createNotFoundException('Unable to find Page entity.');
        }

        if (true !== $this->isCsrfTokenValid($page->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $nodeRepository = $this->get('vivo_page.repository.menu_node');

            foreach ($page->getMenuNodes() as $node) {
                $nodeRepository->removeFromTree($node);
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($page);
            $em->flush();
            $this->addFlash('success', 'admin.flash.page.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_page.admin.tree.index');
    }

    /**
     * @return SearchList
     */
    protected function getSearchList($context = null)
    {
        $searchList = new SearchList();
        $searchList->setEqualColumns(array('page.id'))
            ->setLikeColumns(array('page.pageTitle', 'slug.slug', 'page.metaTitle', 'page.metaDescription', 'content.content'));

        return $searchList;
    }
}
