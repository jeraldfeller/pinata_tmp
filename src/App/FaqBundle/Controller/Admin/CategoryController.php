<?php

namespace App\FaqBundle\Controller\Admin;

use App\FaqBundle\Entity\Category;
use App\FaqBundle\Form\Type\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vivo\UtilBundle\Form\Model\SearchList;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Category controller.
 */
class CategoryController extends Controller
{
    /**
     * Lists all Category entities.
     */
    public function indexAction(Request $request)
    {
        $categoryRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Category');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $categoryRepository->createQueryBuilder('category')
            ->orderBy('category.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('category.id'))
            ->setLikeColumns(array('category.title'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppFaq/Admin/Category/index.html.twig', array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 25),
            )
        );
    }

    /**
     * Creates a new Category entity.
     */
    public function createAction(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(new CategoryType(), $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'admin.flash.category.create.success');

            return $this->redirect($this->generateUrl('app_faq.admin.category.index'));
        }

        return $this->render('@AppFaq/Admin/Category/create.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     */
    public function updateAction(Request $request, $id)
    {
        $categoryRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Category');
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $form = $this->createForm(new CategoryType(), $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'admin.flash.category.update.success');

            return $this->redirect($this->generateUrl('app_faq.admin.category.index'));
        }

        return $this->render('@AppFaq/Admin/Category/update.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $categoryRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Category');
        /** @var \App\FaqBundle\Entity\Category $category */
        $category = $categoryRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($category->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            if (count($category->getFaqs()) > 0) {
                $this->addFlash('error', 'admin.flash.category.delete.notEmpty');

                return $this->redirect($this->generateUrl('app_faq.admin.category.index'));
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'admin.flash.category.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirect($this->generateUrl('app_faq.admin.category.index'));
    }

    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'AppFaqBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }
}
