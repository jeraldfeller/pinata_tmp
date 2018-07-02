<?php

namespace App\FaqBundle\Controller\Admin;

use App\FaqBundle\Entity\Faq;
use App\FaqBundle\Form\Type\FaqType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Vivo\UtilBundle\Form\Model\SearchList;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Faq controller.
 */
class FaqController extends Controller
{
    /**
     * Lists all Faq entities.
     */
    public function indexAction(Request $request)
    {
        $faqRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Faq');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $faqRepository->createQueryBuilder('faq')
            ->addSelect('category')
            ->leftJoin('faq.category', 'category')
            ->orderBy('faq.updatedAt', 'desc');

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('faq.id'))
            ->setLikeColumns(array('category.title', 'faq.question', 'faq.answer'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppFaq/Admin/Faq/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 25),
            )
        );
    }

    /**
     * Lists all Faq entities.
     */
    public function rankAction(Request $request)
    {
        /** @var \App\FaqBundle\Repository\CategoryRepository $categoryRepository */
        $categoryRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Category');
        $faqRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Faq');

        $categories = $categoryRepository->findCategoriesWithFaqsQueryBuilder();

        $postRank = 0;

        if ($request->isMethod('POST')) {
            parse_str($request->request->get('ranks'), $tree);

            foreach ($tree as $type => $results) {
                if ('app_faq' === $type) {
                    $categoryRank = 0;

                    foreach ($results as $categoryId => $parent) {
                        if ($categoryId && ($category = $categoryRepository->find($categoryId))) {
                            $category->setRank($categoryRank++);
                        } else {
                            $this->addFlash('error', 'admin.flash.faq.rank.failed');

                            return $this->redirect($this->generateUrl('app_faq.admin.faq.index'));
                        }
                    }
                } elseif (preg_match('/^app_faq_/', $type)) {
                    foreach ($results as $faqId => $categoryId) {
                        $faq = $faqId ? $faqRepository->find($faqId) : null;
                        $category = $categoryId ? $categoryRepository->find($categoryId) : null;

                        if ($faq && $category) {
                            $faq->setCategory($category);
                            $faq->setRank($postRank++);
                        } else {
                            $this->addFlash('error', 'admin.flash.faq.rank.failed');

                            return $this->redirect($this->generateUrl('app_faq.admin.faq.index'));
                        }
                    }
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'admin.flash.faq.rank.success');

            return $this->redirect($this->generateUrl('app_faq.admin.faq.index'));
        }

        return $this->render('@AppFaq/Admin/Faq/rank.html.twig', array(
            'categories' => $categories,
        ));
    }

    /**
     * Creates a new Faq entity.
     */
    public function createAction(Request $request)
    {
        $faq = new Faq();
        $form = $this->createForm(new FaqType(), $faq);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();
            $this->addFlash('success', 'admin.flash.faq.create.success');

            return $this->redirect($this->generateUrl('app_faq.admin.faq.index'));
        }

        return $this->render('@AppFaq/Admin/Faq/create.html.twig', array(
            'faq' => $faq,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Faq entity.
     */
    public function updateAction(Request $request, $id)
    {
        $faqRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Faq');
        $faq = $faqRepository->find($id);

        if (!$faq) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        $form = $this->createForm(new FaqType(), $faq);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($faq);
            $em->flush();
            $this->addFlash('success', 'admin.flash.faq.update.success');

            return $this->redirect($this->generateUrl('app_faq.admin.faq.index'));
        }

        return $this->render('@AppFaq/Admin/Faq/update.html.twig', array(
            'faq' => $faq,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Faq entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $faqRepository = $this->getDoctrine()->getRepository('AppFaqBundle:Faq');
        $faq = $faqRepository->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$faq) {
            throw $this->createNotFoundException('Unable to find Faq entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($faq->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($faq);
            $em->flush();
            $this->addFlash('success', 'admin.flash.faq.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirect($this->generateUrl('app_faq.admin.faq.index'));
    }

    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'AppFaqBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }
}
