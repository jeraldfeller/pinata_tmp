<?php

namespace App\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\BlogBundle\Event\Events;
use Vivo\BlogBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\BlogBundle\Controller\PostController as BasePost;

class PostController extends BasePost
{
    public function indexAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();
        $response->setSharedMaxAge(30);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');
        $paginator = $this->get('app_core.knp_paginator');

        $queryBuilder = $postRepository->getActivePostsWithCategoryQueryBuilder();

        $page = $request->query->get('page', 1);

        if ($page == 1) {
            $postsPerPage = 11;
        } else {
            $postsPerPage = 10;
        }

        $pagination = $paginator->paginate($queryBuilder, $page, $postsPerPage);

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new PrepareSeoEvent($cmsPage, $request);
        $event->setPagination($pagination);
        $eventDispatcher->dispatch(Events::PREPARE_SEO, $event);

        return $this->render('@VivoBlog/Post/index.html.twig', array(
            'page' => $cmsPage,
            'posts' => $pagination,
        ), $response);
    }

    public function featureAction($max = 8)
    {
        $postRepository = $this->getDoctrine()->getRepository('AppBlogBundle:Post');
        $posts = $postRepository->findBy(array(), null, intval($max));

        return $this->render('@AppBlog/Post/feature.html.twig', array(
                'posts' => $posts,
            ));
    }

    public function chatterAction($max = 3)
    {
        $postRepository = $this->get('app_blog.repository.post');
        $posts = $postRepository->findAllFeatured(array(), null, intval($max));

        return $this->render('@AppBlog/Post/homepage.html.twig', array(
            'posts' => $posts,
        ));
    }
}
