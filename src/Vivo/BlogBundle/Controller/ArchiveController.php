<?php

namespace Vivo\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\BlogBundle\Event\Events;
use Vivo\BlogBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;

class ArchiveController extends Controller
{
    public function indexAction(Request $request, PageInterface $cmsPage, $year, $month)
    {
        $response = new Response();
        $response->setSharedMaxAge(30);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');
        $paginator = $this->get('knp_paginator');

        $from = new \DateTime($year.'-'.$month.'-01 00:00:00');
        $to = clone $from;
        $to->modify('last day of this month 23:59:59');

        $queryBuilder = $postRepository->getActivePostsWithCategoryQueryBuilder()
            ->andWhere('post.publicationDate >= :archive_from and post.publicationDate <= :archive_to')
            ->setParameter('archive_from', $from)
            ->setParameter('archive_to', $to);

        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
        $pagination = $paginator->paginate($queryBuilder, $request->query->get('page', 1));

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $event = new PrepareSeoEvent($cmsPage, $request);
        $event->setArchiveDate($from);
        $event->setPagination($pagination);
        $eventDispatcher->dispatch(Events::PREPARE_SEO, $event);

        return $this->render('@VivoBlog/Post/index.html.twig', array(
            'page' => $cmsPage,
            'posts' => $pagination,
            'archive_date' => $from,
        ), $response);
    }

    public function sidePanelAction(Request $request, $selected_date = null, $number_of_months, $count_posts, $show_active_only)
    {
        $response = new Response();
        $response->setSharedMaxAge(30);
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Vivo\BlogBundle\Repository\PostRepositoryInterface $postRepository */
        $postRepository = $this->get('vivo_blog.repository.post');

        $number_of_months = max(1, $number_of_months);

        $from = new \DateTime('-'.$number_of_months.' months');
        $from->modify('first day of next month midnight');

        $to = new \DateTime('last day of last month 23:59:59');

        $current = clone $to;
        $months = array();
        while ($current > $from) {
            $months[$current->format('Y-m')] = array(
                'date' => clone $current,
                'post_count' => 0,
            );

            $current->modify('-1 month');
        }

        // Only run the query if we are counting or showing active posts
        if ($count_posts || $show_active_only) {
            $activeMonths = $postRepository->createQueryBuilder('post')
                ->select('count(post.id) as post_count, SUBSTRING(post.publicationDate, 1, 7) as groupedDate')
                ->andWhere('post.publicationDate >= :publication_date_from and post.publicationDate <= :publication_date_to')
                ->setParameter('publication_date_from', $from)
                ->setParameter('publication_date_to', $to)
                ->groupBy('groupedDate')
                ->getQuery()
                ->useQueryCache(true)
                ->useResultCache(true, 900)
                ->getArrayResult();

            foreach ($activeMonths as $month) {
                if (isset($months[$month['groupedDate']])) {
                    $months[$month['groupedDate']]['post_count'] = $month['post_count'];
                }
            }
        }

        if ($show_active_only) {
            foreach ($months as $key => $val) {
                if ($val['post_count'] < 1) {
                    unset($months[$key]);
                }
            }
        }

        return $this->render('@VivoBlog/Archive/side_panel.html.twig', array(
            'selected_date' => null === $selected_date ?: new \DateTime($selected_date),
            'months' => array_values($months),
            'count_posts' => $count_posts,
        ), $response);
    }
}
