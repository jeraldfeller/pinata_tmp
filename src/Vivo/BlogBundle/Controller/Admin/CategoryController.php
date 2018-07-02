<?php

namespace Vivo\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
        /** @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->get('vivo_blog.repository.category');
        $paginator = $this->get('knp_paginator');

        $searchList = $this->container->get('vivo_blog.search.model.category');

        $form = $this->createForm('Vivo\BlogBundle\Form\Type\CategorySearchType', $searchList);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $form->submit(
                array_intersect_key($request->query->all(), array_flip(array_keys($form->all())))
            );
        }

        $queryBuilder = $searchList->getSearchQueryBuilder($categoryRepository->createAdminListQueryBuilder());

        switch ($sort = $request->query->get('sort')) {
            case 'postCount':
                $queryBuilder->addSelect('size(category.posts) as HIDDEN '.$sort);
                break;
        }

        $pagination = $paginator->paginate($queryBuilder, $request->query->get('page', 1), 25);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoBlog/Admin/Category/index.html.twig', array(
                'searchForm' => $form->createView(),
                'pagination' => $pagination,
                'post_count' => $categoryRepository->countPostsForCategories(iterator_to_array($pagination)),
            )
        );
    }

    /**
     * Rank Group entities.
     */
    public function rankAction(Request $request)
    {
        /** @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->get('vivo_blog.repository.category');
        $categories = $categoryRepository->findAll();

        if ($request->isMethod('POST')) {
            if (!$request->request->get('ranks')) {
                $this->addFlash('info', 'admin.flash.category.rank.none');
            } else {
                parse_str($request->request->get('ranks'), $ranks);

                if (!isset($ranks['vivo_blog_category']) || !is_array($ranks['vivo_blog_category']) || count($ranks['vivo_blog_category']) !== count($categories)) {
                    $this->addFlash('error', 'admin.flash.category.rank.invalid');
                } else {
                    $rank = 0;
                    foreach ($ranks['vivo_blog_category'] as $categoryId => $parent) {
                        $categoryId = (int) $categoryId;

                        if ($categoryId) {
                            /** @var \Vivo\BlogBundle\Model\CategoryInterface $category */
                            if ($category = $categoryRepository->find($categoryId)) {
                                $category->setRank(++$rank);
                            }
                        }
                    }

                    if ($rank > 0) {
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $this->addFlash('success', 'admin.flash.category.rank.success');
                    } else {
                        $this->addFlash('success', 'admin.flash.category.rank.none');
                    }
                }
            }

            return $this->redirectToRoute('vivo_blog.admin.category.rank');
        }

        return $this->render('@VivoBlog/Admin/Category/rank.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Creates a new Category entity.
     */
    public function createAction(Request $request)
    {
        /** @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->get('vivo_blog.repository.category');
        $category = $categoryRepository->createCategory();

        $form = $this->createForm('Vivo\BlogBundle\Form\Type\CategoryType', $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'admin.flash.category.create.success');

            return $this->redirectToRoute('vivo_blog.admin.category.index');
        }

        return $this->render('@VivoBlog/Admin/Category/create.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Category entity.
     */
    public function updateAction(Request $request, $id)
    {
        $categoryRepository = $this->get('vivo_blog.repository.category');
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        $form = $this->createForm('Vivo\BlogBundle\Form\Type\CategoryType', $category);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'admin.flash.category.update.success');

            return $this->redirectToRoute('vivo_blog.admin.category.index');
        }

        return $this->render('@VivoBlog/Admin/Category/update.html.twig', array(
            'category' => $category,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Category entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $categoryRepository = $this->get('vivo_blog.repository.category');
        /** @var \Vivo\BlogBundle\Model\CategoryInterface $category */
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }

        if (true !== $this->isCsrfTokenValid($category->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            if (count($category->getPosts()) > 0) {
                $this->addFlash('error', 'admin.flash.category.delete.notEmpty');

                return $this->redirectToRoute('vivo_blog.admin.category.index');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'admin.flash.category.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_blog.admin.category.index');
    }

    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'VivoBlogBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }
}
