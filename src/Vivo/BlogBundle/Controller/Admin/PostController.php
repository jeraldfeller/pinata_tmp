<?php

namespace Vivo\BlogBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vivo\BlogBundle\Model\Post;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Post controller.
 */
class PostController extends Controller
{
    /**
     * Lists all Post entities.
     */
    public function indexAction(Request $request)
    {
        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');
        $paginator = $this->get('knp_paginator');

        $searchList = $this->container->get('vivo_blog.search.model.post');

        $form = $this->createForm('Vivo\BlogBundle\Form\Type\PostSearchType', $searchList);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $form->submit(
                array_intersect_key($request->query->all(), array_flip(array_keys($form->all())))
            );
        }

        $queryBuilder = $searchList->getSearchQueryBuilder($postRepository->createAdminListQueryBuilder());

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoBlog/Admin/Post/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($queryBuilder, $request->query->get('page', 1), 25),
                'include_author_field' => $this->container->getParameter('vivo_blog.include_author_field'),
            )
        );
    }

    /**
     * Creates a new Post entity.
     */
    public function createAction(Request $request)
    {
        $post = $this->createPost();
        $post->setOwner($this->getUser(), true);

        $form = $this->createForm('Vivo\BlogBundle\Form\Type\PostType', $post);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'admin.flash.post.create.success');

            return $this->redirectToRoute('vivo_blog.admin.post.index');
        }

        return $this->render('@VivoBlog/Admin/Post/create.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Post entity.
     */
    public function updateAction(Request $request, $id)
    {
        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $form = $this->createForm('Vivo\BlogBundle\Form\Type\PostType', $post);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'admin.flash.post.update.success');

            return $this->redirectToRoute('vivo_blog.admin.post.index');
        }

        return $this->render('@VivoBlog/Admin/Post/update.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Post entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $postRepository = $this->get('vivo_blog.repository.post');
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        if (true !== $this->isCsrfTokenValid($post->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'admin.flash.post.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_blog.admin.post.index');
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

    /**
     * Create post.
     *
     * @return \Vivo\BlogBundle\Model\PostInterface
     */
    protected function createPost()
    {
        return $this->get('vivo_blog.repository.post')->createPost();
    }
}
