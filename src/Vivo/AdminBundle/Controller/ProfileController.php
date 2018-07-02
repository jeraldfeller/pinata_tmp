<?php

namespace Vivo\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Vivo\AdminBundle\Event\Events;
use Vivo\AdminBundle\Event\PasswordChangedEvent;
use Vivo\AdminBundle\Form\Model\PasswordChange;

/**
 * Profile controller.
 */
class ProfileController extends BaseController
{
    /**
     * Edit profile.
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm('Vivo\AdminBundle\Form\Type\ProfileType', new PasswordChange($user));
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($form->getData()->changePassword) {
                $user->setPlainPassword($form->getData()->newPassword);

                /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
                $eventDispatcher = $this->get('event_dispatcher');
                $eventDispatcher->dispatch(Events::PASSWORD_CHANGED, new PasswordChangedEvent($user, $user));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'flash.profile.update.success');

            return $this->redirectToRoute('vivo_admin.profile.edit');
        }

        return $this->render('@VivoAdmin/Profile/edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    public function passwordExpiredAction(Request $request)
    {
        /** @var \Vivo\AdminBundle\Model\UserInterface $user */
        $user = $this->getUser();

        if (!$user->isPasswordExpired() || !$this->container->getParameter('vivo_admin.password_expiry.enabled')) {
            return $this->redirect($this->getExpiredReturnUrl($request));
        }

        $form = $this->createForm('Vivo\AdminBundle\Form\Type\PasswordExpiredType', new PasswordChange($user));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user->setPlainPassword($form->getData()->newPassword)
                ->setPasswordExpiredAt(null);

            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
            $eventDispatcher = $this->get('event_dispatcher');
            $eventDispatcher->dispatch(Events::PASSWORD_CHANGED, new PasswordChangedEvent($user, $user));

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'flash.profile.passwordExpiry.updated');

            return $this->redirect($this->getExpiredReturnUrl($request));
        }

        return $this->render('@VivoAdmin/Profile/password_expired.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @return string
     */
    protected function getExpiredReturnUrl(Request $request)
    {
        $targetQueryParameter = $this->container->getParameter('vivo_admin.password_expiry.target_query_parameter');
        $csrfQueryParameter = $this->container->getParameter('vivo_admin.password_expiry.csrf_query_parameter');

        if ($request->query->get($targetQueryParameter) && $this->isCsrfTokenValid($request->query->get($targetQueryParameter), $request->query->get($csrfQueryParameter))) {
            $url = $request->query->get($targetQueryParameter);
        } else {
            $url = $this->container->get('router')->generate('admin_homepage');
        }

        return $url;
    }
}
