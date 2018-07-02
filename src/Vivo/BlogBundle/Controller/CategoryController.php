<?php

namespace Vivo\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Vivo\BlogBundle\Event\Events;
use Vivo\BlogBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;

class CategoryController extends Controller
{
    public function indexAction(Request $request, PageInterface $cmsPage, $slug)
    {
        $response = new Response();
        $response->setSharedMaxAge(30);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->get('vivo_blog.repository.category');
        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');
        $paginator = $this->get('knp_paginator');

        if (!$category = $categoryRepository->findOneBySlug($slug)) {
            return $this->redirectToRoute('vivo_blog.post.index', array(), Response::HTTP_MOVED_PERMANENTLY);
        }

        if ($slug !== $category->getSlug()) {
            return $this->redirectToRoute('vivo_blog.category.index', array('slug' => $category->getSlug()), Response::HTTP_MOVED_PERMANENTLY);
        }

        $queryBuilder = $postRepository->getActivePostsWithCategoryQueryBuilder($category);
        $pagination = $paginator->paginate($queryBuilder, $request->query->get('page', 1));

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new PrepareSeoEvent($cmsPage, $request);
        $event->setCategory($category);
        $event->setPagination($pagination);
        $eventDispatcher->dispatch(Events::PREPARE_SEO, $event);

        return $this->render('@VivoBlog/Post/index.html.twig', array(
            'page' => $cmsPage,
            'category' => $category,
            'posts' => $pagination,
        ), $response);
    }

    public function viewAction(Request $request, PageInterface $cmsPage, $category_slug, $post_slug)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->get('vivo_blog.repository.category');
        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');

        $post = $postRepository->findOneBySlug($post_slug);

        if (!$post) {
            return $this->redirectToRoute('vivo_blog.category.index', array('slug' => $category_slug), Response::HTTP_MOVED_PERMANENTLY);
        } elseif (!$post->isActive()) {
            throw new ServiceUnavailableHttpException(86400, sprintf("Post '%d' is currently inactive.", $post->getId()));
        }

        $category = $categoryRepository->findOneBySlug($category_slug);

        if (!$category || !$post->hasCategory($category)) {
            if ($firstCategory = $post->getCategories()->first()) {
                // Category does not exist - Redirect to first category
                return $this->redirectToRoute('vivo_blog.category.view', array('category_slug' => $firstCategory->getSlug(), 'post_slug' => $post->getSlug()), Response::HTTP_MOVED_PERMANENTLY);
            } else {
                // No categories - Redirect to blog view page
                return $this->redirectToRoute('vivo_blog.post.view', array(
                    'year' => $post->getPublicationDate()->format('Y'),
                    'month' => $post->getPublicationDate()->format('m'),
                    'day' => $post->getPublicationDate()->format('d'),
                    'slug' => $post->getSlug(),
                ), Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        if ($category_slug !== $category->getSlug() || $post_slug !== $post->getSlug()) {
            return $this->redirectToRoute('vivo_blog.category.view', array('category_slug' => $category->getSlug(), 'post_slug' => $post->getSlug()), Response::HTTP_MOVED_PERMANENTLY);
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new PrepareSeoEvent($cmsPage, $request);
        $event->setPost($post);
        $event->setCategory($category);
        $eventDispatcher->dispatch(Events::PREPARE_SEO, $event);

        return $this->render('@VivoBlog/Post/view.html.twig', array(
            'page' => $cmsPage,
            'category' => $category,
            'post' => $post,
            'include_author' => $this->container->getParameter('vivo_blog.include_author_field'),
        ), $response);
    }

    public function sidePanelAction(Request $request, $selected_category_id = null, $count_posts, $show_active_only)
    {
        $response = new Response();
        $response->setSharedMaxAge(30);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\CategoryRepositoryInterface $categoryRepository */
        $categoryRepository = $this->get('vivo_blog.repository.category');

        if ($count_posts || $show_active_only) {
            $categories = $categoryRepository->findAllWithPosts();
        } else {
            $categories = $categoryRepository->findAll();
        }

        if ($show_active_only) {
            foreach ($categories as $key => $val) {
                if ($val->getActivePosts()->count() < 1) {
                    unset($categories[$key]);
                }
            }
        }

        return $this->render('@VivoBlog/Category/side_panel.html.twig', array(
            'categories' => $categories,
            'selected_category' => $selected_category_id ? $categoryRepository->find($selected_category_id) : null,
            'count_posts' => $count_posts,
        ), $response);
    }
}
