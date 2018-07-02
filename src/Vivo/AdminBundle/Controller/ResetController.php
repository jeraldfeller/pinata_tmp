<?php

namespace Vivo\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vivo\AdminBundle\Event\PasswordChangedEvent;
use Vivo\AdminBundle\Form\Model\PasswordCreate;
use Vivo\AdminBundle\Model\UserInterface;
use Vivo\AdminBundle\Event\Events;

/**
 * Reset controller.
 */
class ResetController extends BaseController
{
    /**
     * Request password reset token.
     */
    public function requestAction(Request $request)
    {
        if ($user = $this->getUser(false)) {
            return $this->redirect($this->getRedirectionUrl($user));
        }

        $error = null;
        $lastUsername = null;

        if ($request->isMethod('POST')) {
            /** @var \Vivo\AdminBundle\Repository\UserRepositoryInterface $userRepository */
            $userRepository = $this->get('vivo_admin.repository.user');
            $user = $userRepository->findOneByUsername($request->request->get('_username'));

            if ($user) {
                if ($user->isEnabled()) {
                    $ttl = $this->container->getParameter('vivo_admin.reset_ttl');
                    if (!$user->isPasswordResetRequestNonExpired($ttl * 0.75)) {
                        $user->setPasswordResetRequestAt(new \DateTime('now'));
                    }

                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();

                    $this->container->get('vivo_admin.util.mailer')->sendPasswordResetRequestEmail($user);

                    return $this->redirectToRoute('vivo_admin.reset.sent');
                } else {
                    $error = 'form.reset.disabled';
                }
            } else {
                $error = 'form.reset.invalidUser';
            }

            $lastUsername = $request->request->get('_username');
        }

        if (null === $lastUsername) {
            /** @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils */
            $authenticationUtils = $this->get('security.authentication_utils');

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();
        }

        return $this->render('@VivoAdmin/Reset/request.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
        ));
    }

    /**
     * Inform user password reset url has been sent.
     */
    public function sentAction()
    {
        if ($user = $this->getUser(false)) {
            return $this->redirect($this->getRedirectionUrl($user));
        }

        return $this->render('@VivoAdmin/Reset/sent.html.twig');
    }

    /**
     * Reset password.
     */
    public function resetPasswordAction(Request $request, $id, $token)
    {
        if ($user = $this->getUser(false)) {
            return $this->redirect($this->getRedirectionUrl($user));
        }

        /* @var \Vivo\AdminBundle\Repository\UserRepositoryInterface $userProvider */
        $userRepository = $this->get('vivo_admin.repository.user');
        /** @var \Vivo\AdminBundle\Model\UserInterface $user */
        $user = $userRepository->findOneById($id);

        if ($user) {
            $ttl = $this->container->getParameter('vivo_admin.reset_ttl');
            if (!$user->isPasswordResetRequestNonExpired($ttl)) {
                $this->addFlash('error', 'flash.reset.expired');

                return $this->redirectToRoute('vivo_admin.reset.request');
            }
        }

        if (!$user || true !== hash_equals($user->getPasswordResetToken(), $token)) {
            $this->addFlash('error', 'flash.reset.invalid');

            return $this->redirectToRoute('vivo_admin.reset.request');
        }

        $passwordCreate = new PasswordCreate($user);
        $form = $this->createForm('Vivo\AdminBundle\Form\Type\PasswordResetType', $passwordCreate);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setPlainPassword($passwordCreate->newPassword)
                ->setPasswordExpiredAt(null);

            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $eventDispatcher->dispatch(Events::PASSWORD_CHANGED, new PasswordChangedEvent($user, $user));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'flash.reset.updated');

            $this->get('vivo_admin.security.user_authenticator')->authenticateUser($user, $request);

            return $this->redirect($this->getRedirectionUrl($user));
        }

        return $this->render('@VivoAdmin/Reset/reset.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param UserInterface $user
     *
     * @return string
     */
    protected function getRedirectionUrl(UserInterface $user)
    {
        return $this->get('router')->generate('admin_homepage');
    }
}
