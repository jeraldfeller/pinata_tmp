<?php

namespace Vivo\PageBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
        $value = $this->get('translator')->trans($translationKey, array(), 'VivoPageBundle');
        $this->get('session')->getFlashBag()->add($type, $value);
    }
}
