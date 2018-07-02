<?php

namespace Vivo\AdminBundle\Controller;

/**
 * Security controller.
 */
class SecurityController extends BaseController
{
    /**
     * Handle user login.
     */
    public function loginAction()
    {
        $template = 'login';
        if ($this->getUser(false) && $this->getUser(false)->isEnabled()) {
            if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && !$this->isGranted('IS_AUTHENTICATED_FULLY')) {
                $template = 'verify';
            } else {
                return $this->redirectToRoute('admin_homepage');
            }
        }

        /** @var \Symfony\Component\Security\Http\Authentication\AuthenticationUtils $authenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('@VivoAdmin/Security/'.$template.'.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ));
    }

    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }
}
