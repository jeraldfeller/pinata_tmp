<?php

namespace App\CoreBundle\Controller\Admin;

use App\CoreBundle\Entity\Farm;
use App\CoreBundle\Entity\FarmSlug;
use App\CoreBundle\Form\Type\Admin\FarmType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\UtilBundle\Util\Urlizer;

class FarmController extends Controller
{
    /**
     * Lists all farm entities.
     */
    public function indexAction(Request $request)
    {
        $farmRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Farm');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $farmRepository->createQueryBuilder('farm')
            ->select('farm')
            ->orderBy('farm.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('farm.id'))
            ->setLikeColumns(array('farm.name'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppCore/Admin/Farm/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new Farm entity.
     */
    public function createAction(Request $request)
    {
        $farm = new Farm();

        $form = $this->createForm(new FarmType('App\CoreBundle\Entity\FarmSlug'), $farm);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (null === $farm->getPrimarySlug()) {
                $slug = new FarmSlug();
                $slug->setSlug(Urlizer::urlize($farm->getName()));
                $farm->setPrimarySlug($slug);
            }

            $em->persist($farm);
            $em->flush();
            $this->addFlash('success', 'admin.flash.farm.create.success');

            return $this->redirect($this->generateUrl('app_core.admin.farm.index'));
        }

        return $this->render('@AppCore/Admin/Farm/create.html.twig', array(
                'farm' => $farm,
                'form' => $form->createView(),
            ));
    }

    /**
     * Updates a Farm entity.
     */
    public function updateAction(Request $request, $id)
    {
        /** @var \App\CoreBundle\Repository\FarmRepository $farmRepository */
        $farmRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Farm');
        $farm = $farmRepository->findOneById($id);

        if (!$farm) {
            throw $this->createNotFoundException('Unable to find branch entity.');
        }

        if ($request->isMethod('POST')) {
        }

        $form = $this->createForm(new FarmType('App\CoreBundle\Entity\FarmSlug'), $farm);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($farm);

            $em->flush();
            $this->addFlash('success', 'admin.flash.farm.update.success');

            return $this->redirect($this->generateUrl('app_core.admin.farm.index'));
        }

        return $this->render('@AppCore/Admin/Farm/update.html.twig', array(
                'farm' => $farm,
                'form' => $form->createView(),
            ));
    }

    /**
     * Deletes a Store entity.
     */
    public function deleteAction($id, $token)
    {
        $farmRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Farm');
        $farm = $farmRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$farmRepository) {
            throw $this->createNotFoundException('Unable to find branch entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($farm->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($farm);
            $em->flush();
            $this->addFlash('success', 'admin.flash.farm.delete.success');
        }

        return $this->redirect($this->generateUrl('app_core.admin.farm.index'));
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
     * Rank farm entities.
     */
    public function rankAction(Request $request)
    {
        /** @var \App\CoreBundle\Repository\FarmRepository $farmRepository */
        $farmRepository = $this->getDoctrine()->getRepository('AppCoreBundle:Farm');
        $farms = $farmRepository->findAll();

        if ($request->isMethod('POST')) {
            if (!$request->request->get('ranks')) {
                $this->addFlash('info', 'admin.flash.farm.rank.none');
            } else {
                parse_str($request->request->get('ranks'), $ranks);

                if (!isset($ranks['app_core_farm']) || !is_array($ranks['app_core_farm']) || count($ranks['app_core_farm']) !== count($farms)) {
                    $this->addFlash('error', 'admin.flash.farm.rank.invalid');
                } else {
                    $rank = 0;
                    foreach ($ranks['app_core_farm'] as $farmId => $parent) {
                        $farmId = (int) $farmId;

                        if ($farmId) {
                            /** @var \App\CoreBundle\Entity\Farm $farm */
                            if ($farm = $farmRepository->find($farmId)) {
                                $farm->setRank(++$rank);
                            }
                        }
                    }

                    if ($rank > 0) {
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $this->addFlash('success', 'admin.flash.farm.rank.success');
                    } else {
                        $this->addFlash('success', 'admin.flash.farm.rank.none');
                    }
                }
            }

            return $this->redirect($this->generateUrl('app_core.admin.farm.rank'));
        }

        return $this->render('@AppCore/Admin/Farm/rank.html.twig', array(
                'farms' => $farms,
            ));
    }
}
