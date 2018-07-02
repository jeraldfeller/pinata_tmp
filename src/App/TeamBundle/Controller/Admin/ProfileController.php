<?php

namespace App\TeamBundle\Controller\Admin;

use App\TeamBundle\Entity\Profile;
use App\TeamBundle\Entity\ProfileSlug;
use App\TeamBundle\Form\Type\Admin\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\UtilBundle\Util\Urlizer;

class ProfileController extends Controller
{
    /**
     * Lists all profile entities.
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $this->getProfileRepository()->createAdminListQueryBuilder();

        $searchList = new SearchList();
        $searchList->setEqualColumns(array('profile.id'))
            ->setLikeColumns(array('profile.name', 'profile.position', 'profile.description'));

        $form = $this->createForm('search_list', $searchList);
        $form->handleRequest($request);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@AppTeam/Admin/Profile/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 30),
            )
        );
    }

    /**
     * Rank Profile entities.
     */
    public function sortAction(Request $request)
    {
        $profileRepository = $this->getProfileRepository();
        $profiles = $profileRepository->findAllSortedForAdmin();

        if ($request->isMethod('POST')) {
            if (!$request->request->get('ranks')) {
                $this->addFlash('info', 'admin.flash.profile.sort.none');
            } else {
                parse_str($request->request->get('ranks'), $ranks);

                $rank = 0;
                foreach ($ranks['app_team_profile'] as $profileId => $parent) {
                    $profileId = (int) $profileId;

                    if ($profileId) {
                        /** @var \App\TeamBundle\Entity\Profile $profile */
                        if ($profile = $profileRepository->find($profileId)) {
                            $profile->setRank(++$rank);
                        }
                    }
                }

                if ($rank > 0) {
                    $em = $this->getDoctrine()->getManager();
                    $em->flush();
                    $this->addFlash('success', 'admin.flash.profile.sort.success');
                } else {
                    $this->addFlash('success', 'admin.flash.profile.sort.none');
                }
            }

            return $this->redirectToRoute('app_team.admin.profile.sort');
        }

        return $this->render('@AppTeam/Admin/Profile/sort.html.twig', array(
            'profiles' => $profiles,
        ));
    }

    /**
     * Creates a new Profile entity.
     */
    public function createAction(Request $request)
    {
        $profile = new Profile();

        $form = $this->createForm(new ProfileType('App\TeamBundle\Entity\ProfileSlug'), $profile);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (null === $profile->getPrimarySlug()) {
                $slug = new ProfileSlug();
                $slug->setSlug(Urlizer::urlize($profile->getName()));
                $profile->setPrimarySlug($slug);
            }
            $em->persist($profile);
            $em->flush();
            $this->addFlash('success', 'admin.flash.profile.create.success');

            return $this->redirectToRoute('app_team.admin.profile.index');
        }

        return $this->render('@AppTeam/Admin/Profile/create.html.twig', array(
            'profile' => $profile,
            'form' => $form->createView(),
        ));
    }

    /**
     * Updates a Profile entity.
     */
    public function updateAction(Request $request, $id)
    {
        $profile = $this->getProfileRepository()->findOneForAdminUpdate($id);

        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }

        $form = $this->createForm(new ProfileType('App\TeamBundle\Entity\ProfileSlug'), $profile);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profile);

            $em->flush();
            $this->addFlash('success', 'admin.flash.profile.update.success');

            return $this->redirectToRoute('app_team.admin.profile.index');
        }

        return $this->render('@AppTeam/Admin/Profile/update.html.twig', array(
            'profile' => $profile,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Profile entity.
     */
    public function deleteAction($id, $token)
    {
        $profile = $this->getProfileRepository()->find($id);
        $csrf = $this->get('form.csrf_provider');

        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }

        if (true !== $csrf->isCsrfTokenValid($profile->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($profile);
            $em->flush();
            $this->addFlash('success', 'admin.flash.profile.delete.success');
        }

        return $this->redirectToRoute('app_team.admin.profile.index');
    }

    /**
     * @return \App\TeamBundle\Repository\ProfileRepository
     */
    protected function getProfileRepository()
    {
        return $this->getDoctrine()->getRepository('AppTeamBundle:Profile');
    }

    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'AppTeamBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }
}
