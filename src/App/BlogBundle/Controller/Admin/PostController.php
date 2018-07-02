<?php

namespace App\BlogBundle\Controller\Admin;

use App\BlogBundle\Entity\Post;
use Vivo\BlogBundle\Controller\Admin\PostController as BasePostController;
use Symfony\Component\HttpFoundation\Request;

class PostController extends BasePostController
{
    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request, $id)
    {
        $postRepository = $this->get('vivo_blog.repository.post');
        /** @var \App\BlogBundle\Entity\Post $post */
        $post = $postRepository->find($id);

        if (!$post) {
            throw $this->createNotFoundException('Unable to find Post entity.');
        }

        $form = $this->createForm('vivo_blog_post_type', $post);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'admin.flash.post.update.success');

            return $this->redirect($this->generateUrl('vivo_blog.admin.post.index'));
        }

        return $this->render('@VivoBlog/Admin/Post/update.html.twig', array(
                'post' => $post,
                'form' => $form->createView(),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function createPost()
    {
        $post = parent::createPost();

        if ($post instanceof Post) {
            $post->setAuthor($this->getUser()->getFullName());
        }

        return $post;
    }
}
