<?php

namespace Vivo\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

/**
 * Group controller.
 */
class GroupController extends BaseController
{
    /**
     * Lists all Group entities.
     */
    public function indexAction(Request $request)
    {
        $this->checkPermissions();
        $groupRepository = $this->get('vivo_admin.repository.group');
        $paginator = $this->get('knp_paginator');

        $searchList = $this->container->get('vivo_admin.search.model.group');

        $form = $this->createForm('Vivo\AdminBundle\Form\Type\GroupSearchType', $searchList);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $form->submit(
                array_intersect_key($request->query->all(), array_flip(array_keys($form->all())))
            );
        }

        $queryBuilder = $groupRepository->getGroupsUnderUserQueryBuilder($this->getUser(), !$this->canEditOwnGroup());
        $queryBuilder = $searchList->getSearchQueryBuilder($queryBuilder);

        switch ($sort = $request->query->get('sort')) {
            case 'userCount':
                $queryBuilder->addSelect('size(user_group.users) as HIDDEN '.$sort);
                break;
        }

        $pagination = $paginator->paginate($queryBuilder, $request->query->get('page', 1), 25);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoAdmin/Group/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $pagination,
                'user_count' => $groupRepository->countUsersForGroups(iterator_to_array($pagination)),
            )
        );
    }

    /**
     * Rank Group entities.
     */
    public function rankAction(Request $request)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        /** @var \Vivo\AdminBundle\Repository\GroupRepositoryInterface $groupRepository */
        $groupRepository = $this->get('vivo_admin.repository.group');
        $groups = $groupRepository->findGroupsUnderUser($this->getUser(), true);
        $currentGroup = $this->getUser()->getGroup();

        if ($request->isMethod('POST')) {
            if (!$request->request->get('ranks')) {
                $this->addFlash('info', 'flash.group.rank.none');
            } else {
                parse_str($request->request->get('ranks'), $ranks);

                if (!isset($ranks['vivo_admin_group']) || !is_array($ranks['vivo_admin_group']) || count($ranks['vivo_admin_group']) !== count($groups)) {
                    $this->addFlash('error', 'flash.group.rank.invalid');
                } else {
                    $changes = false;
                    $groupRank = $currentGroup->getRank();

                    foreach ($ranks['vivo_admin_group'] as $groupId => $parent) {
                        $groupId = (int) $groupId;

                        $group = $groupRepository->findOneGroupByIdUnderUser($groupId, $this->getUser(), !$this->canEditOwnGroup());

                        if ($group && $group !== $currentGroup) {
                            $group->setRank(++$groupRank);
                            $changes = true;
                        }
                    }

                    if ($changes) {
                        $em = $this->getDoctrine()->getManager();
                        $em->flush();
                        $this->addFlash('success', 'flash.group.rank.success');
                    } else {
                        $this->addFlash('info', 'flash.group.rank.none');
                    }
                }
            }

            return $this->redirectToRoute('vivo_admin.group.rank');
        }

        return $this->render('@VivoAdmin/Group/rank.html.twig', array(
            'groups' => $groups,
        ));
    }

    /**
     * Creates a new Group entity.
     */
    public function createAction(Request $request)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        $groupRepository = $this->get('vivo_admin.repository.group');
        $group = $groupRepository->createGroup();
        $form = $this->createForm('Vivo\AdminBundle\Form\Type\GroupType', $group, array(
            'is_developer' => $this->isGranted('ROLE_DEVELOPER') ? true : false,
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();

            $this->addFlash('success', 'flash.group.create.success');

            return $this->redirectToRoute('vivo_admin.group.index');
        }

        return $this->render('@VivoAdmin/Group/create.html.twig', array(
            'group' => $group,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Group entity.
     */
    public function updateAction(Request $request, $id, $token)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        /** @var \Vivo\AdminBundle\Repository\GroupRepositoryInterface $groupRepository */
        $groupRepository = $this->get('vivo_admin.repository.group');
        $group = $groupRepository->findOneGroupByIdUnderUser($id, $this->getUser(), !$this->canEditOwnGroup());

        if (!$group) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        } elseif (true !== $this->isCsrfTokenValid($group->getCsrfIntention('update'), $token)) {
            return $this->redirectToRoute('vivo_admin.group.index');
        }

        $rolesDiff = array_diff($group->getRoles(), $this->getUser()->getRoles());

        $form = $this->createForm('Vivo\AdminBundle\Form\Type\GroupType', $group, array(
            'is_developer' => $this->isGranted('ROLE_DEVELOPER') ? true : false,
        ));
        $form->handleRequest($request);

        if ($form->isValid()) {
            // Merge the roles that the current user is missing
            $group->setRoles(array_merge($group->getRoles(), $rolesDiff));

            $em = $this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            $this->addFlash('success', 'flash.group.update.success');

            return $this->redirectToRoute('vivo_admin.group.index');
        }

        return $this->render('@VivoAdmin/Group/update.html.twig', array(
            'group' => $group,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a Group entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        /** @var \Vivo\AdminBundle\Repository\GroupRepositoryInterface $groupRepository */
        $groupRepository = $this->get('vivo_admin.repository.group');
        $group = $groupRepository->findOneGroupByIdUnderUser($id, $this->getUser(), !$this->canEditOwnGroup());

        if (!$group) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        if (true !== $this->isCsrfTokenValid($group->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            if (count($group->getUsers()) > 0) {
                $this->addFlash('error', 'flash.group.delete.notEmpty');

                return $this->redirectToRoute('vivo_admin.group.index');
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($group);
            $em->flush();
            $this->addFlash('success', 'flash.group.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_admin.group.index');
    }

    /**
     * Check if user can edit their own group.
     *
     * @return bool
     */
    protected function canEditOwnGroup()
    {
        if ($this->getUser()->hasRole('ROLE_DEVELOPER')) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function checkPermissions()
    {
        if (true !== $this->isGranted('ROLE_USER_GROUP_MANAGEMENT')) {
            throw new AccessDeniedException();
        }
    }
}
