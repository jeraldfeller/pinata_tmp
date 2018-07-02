<?php

namespace App\BlogBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\BlogBundle\Event\Events;
use Vivo\BlogBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;
use Vivo\BlogBundle\Controller\CategoryController as BaseController;

class CategoryController extends BaseController
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
            return $this->redirect($this->generateUrl('vivo_blog.post.index'), 301);
        }

        if ($slug !== $category->getPrimarySlug()->getSlug()) {
            return $this->redirect($this->generateUrl('vivo_blog.category.index', array('slug' => $category->getPrimarySlug()->getSlug())), 301);
        }

        $queryBuilder = $postRepository->getActivePostsWithCategoryQueryBuilder($category);

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
        $event->setCategory($category);
        $event->setPagination($pagination);
        $eventDispatcher->dispatch(Events::PREPARE_SEO, $event);

        return $this->render('@VivoBlog/Post/index.html.twig', array(
            'page' => $cmsPage,
            'category' => $category,
            'posts' => $pagination,
        ), $response);
    }
}
