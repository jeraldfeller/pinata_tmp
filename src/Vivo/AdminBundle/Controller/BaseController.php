<?php

namespace Vivo\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vivo\AdminBundle\Model\UserInterface;

class BaseController extends Controller
{
    /**
     * Add flash message.
     *
     * @param $type
     * @param $translationKey
     */
    protected function addFlash($type, $translationKey)
    {
        $value = $this->get('translator')->trans($translationKey, array(), 'VivoAdminBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }

    /**
     * Verify user has authenticated fully.
     *
     * @throws AccessDeniedException
     */
    protected function mustAuthenticateFully()
    {
        if (true !== $this->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Get current user.
     *
     * @param bool $denyIfNotLoggedIn
     *
     * @return \Vivo\AdminBundle\Model\UserInterface|null
     *
     * @throws AccessDeniedException
     */
    public function getUser($denyIfNotLoggedIn = true)
    {
        $user = parent::getUser();

        if ($denyIfNotLoggedIn) {
            if (!$user || !is_object($user) || !$user instanceof UserInterface) {
                throw new AccessDeniedException('This user does not have access to this section.');
            }
        }

        return $user;
    }
}
