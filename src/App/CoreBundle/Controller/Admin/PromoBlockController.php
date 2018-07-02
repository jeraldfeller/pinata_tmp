<?php

namespace App\CoreBundle\Controller\Admin;

use App\CoreBundle\Entity\PromoBlock;
use App\CoreBundle\Form\Type\Admin\PromoBlockType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;

class PromoBlockController extends Controller
{
    /**
     * Lists all promo entities.
     */
    public function indexAction(Request $request)
    {
        /** @var \App\CoreBundle\Repository\PromoBlockRepository $promoBlockRepository */
        $promoBlockRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlock');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $promoBlockRepository->createQueryBuilder('promo')
            ->orderBy('promo.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('promo.id'))
            ->setLikeColumns(array('promo.name'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppCore/Admin/PromoBlock/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new PromoBlock entity.
     */
    public function createAction(Request $request)
    {
        $promoBlock = new PromoBlock();

        $form = $this->createForm(new PromoBlockType(), $promoBlock);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promoBlock);
            $em->flush();
            $this->addFlash('success', 'admin.flash.promoBlock.create.success');

            return $this->redirect($this->generateUrl('app_core.admin.promo_block.index'));
        }

        return $this->render('@AppCore/Admin/PromoBlock/create.html.twig', array(
            'promo' => $promoBlock,
            'form' => $form->createView(),
        ));
    }

    /**
     * Updates a PromoBlock entity.
     */
    public function updateAction(Request $request, $id)
    {
        /** @var \App\CoreBundle\Repository\PromoBlockRepository $promoBlockRepository */
        $promoBlockRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlock');
        $promoBlock = $promoBlockRepository->find($id);

        if (!$promoBlock) {
            throw $this->createNotFoundException('Unable to find PromoBlock entity.');
        }

        $form = $this->createForm(new PromoBlockType(), $promoBlock);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($promoBlock);

            $em->flush();
            $this->addFlash('success', 'admin.flash.promoBlock.update.success');

            return $this->redirect($this->generateUrl('app_core.admin.promo_block.index'));
        }

        return $this->render('@AppCore/Admin/PromoBlock/update.html.twig', array(
            'promo' => $promoBlock,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a block entity.
     */
    public function deleteAction($id, $token)
    {
        /** @var \App\CoreBundle\Repository\PromoBlockRepository $promoBlockRepository */
        $promoBlockRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlock');
        $promoBlock = $promoBlockRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$promoBlockRepository) {
            throw $this->createNotFoundException('Unable to find PromoBlock entity.');
        }

        /** @var \App\CoreBundle\Repository\PromoBlockGroupRepository $promoBlockGroupRepository */
        $promoBlockGroupRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlockGroup');
        $groups = $promoBlockGroupRepository->findGroupWithBlock($promoBlock);
        if (count($groups) > 0) {
            $this->addFlash('error', 'admin.flash.promoBlock.delete.notEmpty');

            return $this->redirect($this->generateUrl('app_core.admin.promo_block.index'));
        }

        if (true !== $csrf->isCsrfTokenValid($promoBlock->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($promoBlock);
            $em->flush();
            $this->addFlash('success', 'admin.flash.promoBlock.delete.success');
        }

        return $this->redirect($this->generateUrl('app_core.admin.promo_block.index'));
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
}
