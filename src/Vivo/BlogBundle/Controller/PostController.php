<?php

namespace Vivo\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Vivo\BlogBundle\Event\Events;
use Vivo\BlogBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;

class PostController extends Controller
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
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $postRepository->getActivePostsWithCategoryQueryBuilder();
        $pagination = $paginator->paginate($queryBuilder, $request->query->get('page', 1));

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

    public function viewAction(Request $request, PageInterface $cmsPage, $year, $month, $day, $slug)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');
        $post = $postRepository->findOneBySlug($slug);

        if (!$post) {
            return $this->redirectToRoute('vivo_blog.post.index', array(), Response::HTTP_MOVED_PERMANENTLY);
        }

        if (!$post->isActive()) {
            throw new ServiceUnavailableHttpException(86400, sprintf("Post '%d' is currently inactive.", $post->getId()));
        }

        if ($post->getPublicationDate()->format('Ymd') !== $year.$month.$day || $slug !== $post->getSlug()) {
            return $this->redirectToRoute('vivo_blog.post.view', array(
                'year' => $post->getPublicationDate()->format('Y'),
                'month' => $post->getPublicationDate()->format('m'),
                'day' => $post->getPublicationDate()->format('d'),
                'slug' => $post->getSlug(),
            ), Response::HTTP_MOVED_PERMANENTLY);
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new PrepareSeoEvent($cmsPage, $request);
        $event->setPost($post);
        $eventDispatcher->dispatch(Events::PREPARE_SEO, $event);

        return $this->render('@VivoBlog/Post/view.html.twig', array(
            'page' => $cmsPage,
            'post' => $post,
            'archive_date' => $post->getPublicationDate(),
            'include_author' => $this->container->getParameter('vivo_blog.include_author_field'),
        ), $response);
    }
}
