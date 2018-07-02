<?php

namespace Vivo\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\AdminBundle\Event\Events;
use Vivo\AdminBundle\Event\PasswordChangedEvent;
use Vivo\AdminBundle\Form\Model\PasswordCreate;
use Vivo\AdminBundle\Form\Model\PasswordUpdate;

/**
 * Page controller.
 */
class UserController extends BaseController
{
    /**
     * Lists all User entities.
     */
    public function indexAction(Request $request)
    {
        $this->checkPermissions();
        $userRepository = $this->get('vivo_admin.repository.user');
        $paginator = $this->get('knp_paginator');

        $searchList = $this->container->get('vivo_admin.search.model.user');

        $form = $this->createForm('Vivo\AdminBundle\Form\Type\UserSearchType', $searchList);
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $form->submit(
                array_intersect_key($request->query->all(), array_flip(array_keys($form->all())))
            );
        }

        $queryBuilder = $userRepository->getUsersUnderUserQueryBuilder($this->getUser())->orderBy('user.updatedAt', 'DESC');
        $queryBuilder = $searchList->getSearchQueryBuilder($queryBuilder);

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoAdmin/User/index.html.twig',
            array(
                'searchForm' => $form->createView(),
                'pagination' => $paginator->paginate($queryBuilder, $request->query->get('page', 1), 25),
            )
        );
    }

    /**
     * Creates a new User entity.
     */
    public function createAction(Request $request)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        /** @var \Vivo\AdminBundle\Repository\UserRepositoryInterface $userRepository */
        $userRepository = $this->get('vivo_admin.repository.user');
        $passwordCreate = new PasswordCreate($userRepository->createUser());

        $form = $this->createForm('Vivo\AdminBundle\Form\Type\UserCreateType', $passwordCreate);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $deletedUser = $userRepository->findOneByUsername($passwordCreate->user->getEmail());

            if ($deletedUser && $deletedUser->isDeleted()) {
                // Restore deleted users account
                $deletedUser->setDeletedAt(null);

                $passwordCreate->user = $deletedUser;

                $form = $this->createForm('Vivo\AdminBundle\Form\Type\UserCreateType', $passwordCreate);
                $form->handleRequest($request);
            }

            if ($form->isValid()) {
                $passwordCreate->user
                    ->setPlainPassword($passwordCreate->newPassword)
                    ->setPasswordExpiredAt(new \DateTime('now'));

                /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(Events::PASSWORD_CHANGED, new PasswordChangedEvent($passwordCreate->user, $this->getUser()));

                $em = $this->getDoctrine()->getManager();
                $em->persist($passwordCreate->user);
                $em->flush();

                $this->get('vivo_admin.util.mailer')->sendUserCreateEmail($passwordCreate->user, $passwordCreate->newPassword);

                $this->addFlash('success', 'flash.user.create.success');

                return $this->redirectToRoute('vivo_admin.user.index');
            }
        }

        return $this->render('@VivoAdmin/User/create.html.twig', array(
            'user' => $passwordCreate->user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     */
    public function updateAction(Request $request, $id, $token)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        /** @var \Vivo\AdminBundle\Repository\UserRepositoryInterface $userRepository */
        $userRepository = $this->get('vivo_admin.repository.user');
        $user = $userRepository->findOneUserUnderUser($this->getUser(), $id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        } elseif (true !== $this->isCsrfTokenValid($user->getCsrfIntention('update'), $token)) {
            return $this->redirectToRoute('vivo_admin.user.index');
        }

        $passwordUpdate = new PasswordUpdate($user);
        $form = $this->createForm('Vivo\AdminBundle\Form\Type\UserUpdateType', $passwordUpdate);

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($passwordUpdate->updatePassword) {
                $user->setPlainPassword($passwordUpdate->newPassword)
                    ->setPasswordExpiredAt(new \DateTime('now'));

                /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(Events::PASSWORD_CHANGED, new PasswordChangedEvent($user, $this->getUser()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            if ($passwordUpdate->updatePassword) {
                $this->get('vivo_admin.util.mailer')->sendUserPasswordChangedEmail($passwordUpdate->user, $passwordUpdate->newPassword);
            }

            $this->addFlash('success', 'flash.user.update.success');

            return $this->redirectToRoute('vivo_admin.user.index');
        }

        return $this->render('@VivoAdmin/User/update.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $this->checkPermissions();
        $this->mustAuthenticateFully();

        /** @var \Vivo\AdminBundle\Repository\UserRepositoryInterface $userRepository */
        $userRepository = $this->get('vivo_admin.repository.user');
        $user = $userRepository->findOneUserUnderUser($this->getUser(), $id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        if (true !== $this->isCsrfTokenValid($user->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $user->setDeletedAt(new \DateTime('now'))
                ->setGroup(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'flash.user.delete.success');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('vivo_admin.user.index');
    }

    /**
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function checkPermissions()
    {
        if (true !== $this->isGranted('ROLE_USER_MANAGEMENT')) {
            throw new AccessDeniedException();
        }
    }
}
