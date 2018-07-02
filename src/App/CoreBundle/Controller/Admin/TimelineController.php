<?php

namespace App\CoreBundle\Controller\Admin;

use App\CoreBundle\Entity\Timeline;
use App\CoreBundle\Form\Type\Admin\TimelineType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;

class TimelineController extends Controller
{
    /**
     * Lists all timeline entities.
     */
    public function indexAction(Request $request)
    {
        $timelineRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Timeline');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $timelineRepository->createQueryBuilder('timeline')
            ->select('timeline')
            ->orderBy('timeline.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('timeline.id'))
            ->setLikeColumns(array('timeline.name'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppCore/Admin/Timeline/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new Timeline entity.
     */
    public function createAction(Request $request)
    {
        $timeline = new Timeline();

        $form = $this->createForm(new TimelineType('App\CoreBundle\Entity\TimelineSlug'), $timeline);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($timeline);
            $em->flush();
            $this->addFlash('success', 'admin.flash.timeline.create.success');

            return $this->redirect($this->generateUrl('app_core.admin.timeline.index'));
        }

        return $this->render('@AppCore/Admin/Timeline/create.html.twig', array(
            'timeline' => $timeline,
            'form' => $form->createView(),
        ));
    }

    /**
     * Updates a Timeline entity.
     */
    public function updateAction(Request $request, $id)
    {
        /** @var \App\CoreBundle\Repository\TimelineRepository $timelineRepository */
        $timelineRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Timeline');
        $timeline = $timelineRepository->findOneById($id);

        if (!$timeline) {
            throw $this->createNotFoundException('Unable to find branch entity.');
        }

        if ($request->isMethod('POST')) {
        }

        $form = $this->createForm(new TimelineType('App\CoreBundle\Entity\TimelineSlug'), $timeline);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($timeline);

            $em->flush();
            $this->addFlash('success', 'admin.flash.timeline.update.success');

            return $this->redirect($this->generateUrl('app_core.admin.timeline.index'));
        }

        return $this->render('@AppCore/Admin/Timeline/update.html.twig', array(
            'timeline' => $timeline,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Store entity.
     */
    public function deleteAction($id, $token)
    {
        $timelineRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Timeline');
        $timeline = $timelineRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$timelineRepository) {
            throw $this->createNotFoundException('Unable to find branch entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($timeline->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($timeline);
            $em->flush();
            $this->addFlash('success', 'admin.flash.timeline.delete.success');
        }

        return $this->redirect($this->generateUrl('app_core.admin.timeline.index'));
    }

    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'AppCoreBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }

    /**
     * Rank timeline entities.
     */
    public function rankAction(Request $request)
    {
        /** @var \App\CoreBundle\Repository\TimelineRepository $timelineRepository */
        $timelineRepository = $this->getDoctrine()->getRepository('AppCoreBundle:Timeline');
        $timelines = $timelineRepository->findAll();

        if ($request->isMethod('POST')) {
            if (!$request->request->get('ranks')) {
                $this->addFlash('info', 'admin.flash.timeline.rank.none');
            } else {
                parse_str($request->request->get('ranks'), $ranks);

                if (!isset($ranks['app_core_timeline']) || !is_array($ranks['app_core_timeline']) || count($ranks['app_core_timeline']) !== count($timelines)) {
                    $this->addFlash('error', 'admin.flash.timeline.rank.invalid');
                } else {
                    $rank = 0;
                    foreach ($ranks['app_core_timeline'] as $timelineId => $parent) {
                        $timelineId = (int) $timelineId;

                        if ($timelineId) {
                            /** @var \App\CoreBundle\Entity\Timeline $timeline */
                            if ($timeline = $timelineRepository->find($timelineId)) {
                                $timeline->setRank(++$rank);
                            }
                        }
                    }

                    if ($rank > 0) {
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $this->addFlash('success', 'admin.flash.timeline.rank.success');
                    } else {
                        $this->addFlash('success', 'admin.flash.timeline.rank.none');
                    }
                }
            }

            return $this->redirect($this->generateUrl('app_core.admin.timeline.rank'));
        }

        return $this->render('@AppCore/Admin/Timeline/rank.html.twig', array(
            'timelines' => $timelines,
        ));
    }
}
