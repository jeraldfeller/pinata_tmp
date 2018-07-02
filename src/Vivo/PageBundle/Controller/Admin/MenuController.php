<?php

namespace Vivo\PageBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Vivo\UtilBundle\Form\Model\SearchList;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Menu controller.
 */
class MenuController extends BaseController
{
    /**
     * Lists all Menu entities.
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $this->getMenuNodeRepository()
            ->getMenuQueryBuilder()
            ->orderBy('menu_node.updatedAt', 'desc');

        $searchList = $this->getSearchList();

        $form = $this->createForm('Vivo\UtilBundle\Form\Type\SearchListType', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoPage/Admin/Menu/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new Menu entity.
     */
    public function createAction(Request $request)
    {
        $menuNode = $this->getMenuNodeRepository()->createMenuNode();

        $form = $this->createForm('Vivo\PageBundle\Form\Type\MenuType', $menuNode);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuNode);
            $em->flush();
            $this->addFlash('success', 'admin.flash.menu.create.success');

            return $this->redirectToRoute('vivo_page.admin.menu.index');
        }

        return $this->render('@VivoPage/Admin/Menu/create.html.twig', array(
            'menu_node' => $menuNode,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Menu entity.
     */
    public function updateAction(Request $request, $id)
    {
        $menuNodeRepository = $this->get('vivo_page.repository.menu_node');
        $menuNode = $menuNodeRepository->findOneMenu($id);

        if (!$menuNode) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        $form = $this->createForm('Vivo\PageBundle\Form\Type\MenuType', $menuNode);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menuNode);
            $em->flush();
            $this->addFlash('success', 'admin.flash.menu.update.success');

            return $this->redirectToRoute('vivo_page.admin.menu.index');
        }

        return $this->render('@VivoPage/Admin/Menu/update.html.twig', array(
            'menu_node' => $menuNode,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Menu entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $menuNode = $this->getMenuNodeRepository()->findOneMenu($id);

        if (!$menuNode) {
            throw $this->createNotFoundException('Unable to find Menu entity.');
        }

        if (true !== $this->isCsrfTokenValid($menuNode->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            if (count($menuNode->getChildren()) > 1) {
                $this->addFlash('error', 'admin.flash.menu.delete.notEmpty');

                return $this->redirectToRoute('vivo_page.admin.menu.index');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($menuNode);
            $em->flush();
            $this->addFlash('success', 'admin.flash.menu.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_page.admin.menu.index');
    }

    /**
     * @return SearchList
     */
    protected function getSearchList($context = null)
    {
        $searchList = new SearchList();
        $searchList->setEqualColumns(array('menu.id'))
            ->setLikeColumns(array('menu.alias', 'menu.title'));

        return $searchList;
    }

    /**
     * @return \Vivo\PageBundle\Repository\MenuNodeRepository
     */
    protected function getMenuNodeRepository()
    {
        return $this->get('vivo_page.repository.menu_node');
    }
}
