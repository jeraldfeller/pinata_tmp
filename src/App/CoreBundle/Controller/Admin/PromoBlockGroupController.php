<?php

namespace App\CoreBundle\Controller\Admin;

use App\CoreBundle\Entity\PromoBlockGroup;
use App\CoreBundle\Form\Type\Admin\PromoBlockGroupType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;

class PromoBlockGroupController extends Controller
{
    /**
     * Lists all group entities.
     */
    public function indexAction(Request $request)
    {
        /** @var \App\CoreBundle\Repository\PromoBlockGroupRepository $promoBlockGroupRepository */
        $promoBlockGroupRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlockGroup');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $promoBlockGroupRepository->createQueryBuilder('g')
            ->orderBy('g.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('g.id'))
            ->setLikeColumns(array('g.name'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppCore/Admin/PromoBlockGroup/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new PromoBlockGroup entity.
     */
    public function createAction(Request $request)
    {
        $group = new PromoBlockGroup();

        $form = $this->createForm(new PromoBlockGroupType(), $group);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            foreach ($group->getBlocks() as $block) {
                $block->setPromoGroup($group);
            }

            $em->persist($group);
            $em->flush();
            $this->addFlash('success', 'admin.flash.promo_block_group.create.success');

            return $this->redirect($this->generateUrl('app_core.admin.promo_block_group.index'));
        }

        return $this->render('@AppCore/Admin/PromoBlockGroup/create.html.twig', array(
            'group' => $group,
            'form' => $form->createView(),
        ));
    }

    /**
     * Updates a PromoBlockGroup entity.
     */
    public function updateAction(Request $request, $id)
    {
        /** @var \App\CoreBundle\Repository\PromoBlockGroupRepository $promoBlockGroupRepository */
        $promoBlockGroupRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlockGroup');
        $group = $promoBlockGroupRepository->find($id);

        if (!$group) {
            throw $this->createNotFoundException('Unable to find PromoBlockGroup entity.');
        }

        $originalBlocks = new ArrayCollection();

        foreach ($group->getBlocks() as $block) {
            $originalBlocks->add($block);
        }

        $form = $this->createForm(new PromoBlockGroupType(), $group);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // remove the relationship between the tag and the Task
            foreach ($originalBlocks as $block) {
                if (false === $group->getBlocks()->contains($block)) {
                    $em->remove($block);
                }
            }

            foreach ($group->getBlocks() as $block) {
                $block->setPromoGroup($group);
            }

            $em->persist($group);

            $em->flush();
            $this->addFlash('success', 'admin.flash.promo_block_group.update.success');

            return $this->redirect($this->generateUrl('app_core.admin.promo_block_group.index'));
        }

        return $this->render('@AppCore/Admin/PromoBlockGroup/update.html.twig', array(
            'group' => $group,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Store entity.
     */
    public function deleteAction($id, $token)
    {
        /** @var \App\CoreBundle\Repository\PromoBlockGroupRepository $promoBlockGroupRepository */
        $promoBlockGroupRepository = $this->getDoctrine()->getRepository('AppCoreBundle:PromoBlockGroup');
        $group = $promoBlockGroupRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$promoBlockGroupRepository) {
            throw $this->createNotFoundException('Unable to find PromoBlockGroup entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($group->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();
            $this->addFlash('success', 'admin.flash.promo_block_group.delete.success');
        }

        return $this->redirect($this->generateUrl('app_core.admin.promo_block_group.index'));
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
