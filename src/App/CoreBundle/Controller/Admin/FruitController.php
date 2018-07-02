<?php

namespace App\CoreBundle\Controller\Admin;

use App\CoreBundle\Entity\Fruit;
use App\CoreBundle\Form\Type\Admin\FruitType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;

class FruitController extends Controller
{
    /**
     * Lists all fruit entities.
     */
    public function indexAction(Request $request)
    {
        $fruitRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Fruit');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $fruitRepository->createQueryBuilder('fruit')
            ->select('fruit')
            ->orderBy('fruit.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('fruit.id'))
            ->setLikeColumns(array('fruit.name'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppCore/Admin/Fruit/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 50),
            )
        );
    }

    /**
     * Creates a new Fruit entity.
     */
    public function createAction(Request $request)
    {
        $fruit = new Fruit();

        $form = $this->createForm(new FruitType('App\CoreBundle\Entity\FruitSlug'), $fruit);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($fruit);
            $em->flush();
            $this->addFlash('success', 'admin.flash.fruit.create.success');

            return $this->redirect($this->generateUrl('app_core.admin.fruit.index'));
        }

        return $this->render('@AppCore/Admin/Fruit/create.html.twig', array(
            'fruit' => $fruit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Updates a Fruit entity.
     */
    public function updateAction(Request $request, $id)
    {
        /** @var \App\CoreBundle\Repository\FruitRepository $fruitRepository */
        $fruitRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Fruit');
        $fruit = $fruitRepository->findOneById($id);

        if (!$fruit) {
            throw $this->createNotFoundException('Unable to find branch entity.');
        }

        if ($request->isMethod('POST')) {
        }

        $form = $this->createForm(new FruitType('App\CoreBundle\Entity\FruitSlug'), $fruit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($fruit);

            $em->flush();
            $this->addFlash('success', 'admin.flash.fruit.update.success');

            return $this->redirect($this->generateUrl('app_core.admin.fruit.index'));
        }

        return $this->render('@AppCore/Admin/Fruit/update.html.twig', array(
            'fruit' => $fruit,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Store entity.
     */
    public function deleteAction($id, $token)
    {
        $fruitRepository = $this->getDoctrine()->getManager()->getRepository('AppCoreBundle:Fruit');
        $fruit = $fruitRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$fruitRepository) {
            throw $this->createNotFoundException('Unable to find branch entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($fruit->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($fruit);
            $em->flush();
            $this->addFlash('success', 'admin.flash.fruit.delete.success');
        }

        return $this->redirect($this->generateUrl('app_core.admin.fruit.index'));
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
     * Rank fruit entities.
     */
    public function rankAction(Request $request)
    {
        /** @var \App\CoreBundle\Repository\FruitRepository $fruitRepository */
        $fruitRepository = $this->getDoctrine()->getRepository('AppCoreBundle:Fruit');
        $fruits = $fruitRepository->findAll();

        if ($request->isMethod('POST')) {
            if (!$request->request->get('ranks')) {
                $this->addFlash('info', 'admin.flash.fruit.rank.none');
            } else {
                parse_str($request->request->get('ranks'), $ranks);

                if (!isset($ranks['app_core_fruit']) || !is_array($ranks['app_core_fruit']) || count($ranks['app_core_fruit']) !== count($fruits)) {
                    $this->addFlash('error', 'admin.flash.fruit.rank.invalid');
                } else {
                    $rank = 0;
                    foreach ($ranks['app_core_fruit'] as $fruitId => $parent) {
                        $fruitId = (int) $fruitId;

                        if ($fruitId) {
                            /** @var \App\CoreBundle\Entity\Fruit $fruit */
                            if ($fruit = $fruitRepository->find($fruitId)) {
                                $fruit->setRank(++$rank);
                            }
                        }
                    }

                    if ($rank > 0) {
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $this->addFlash('success', 'admin.flash.fruit.rank.success');
                    } else {
                        $this->addFlash('success', 'admin.flash.fruit.rank.none');
                    }
                }
            }

            return $this->redirect($this->generateUrl('app_core.admin.fruit.rank'));
        }

        return $this->render('@AppCore/Admin/Fruit/rank.html.twig', array(
            'fruits' => $fruits,
        ));
    }
}
