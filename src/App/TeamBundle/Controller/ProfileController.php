<?php

namespace App\TeamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;

class ProfileController extends Controller
{
    public function indexAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        /** @var \App\TeamBundle\Repository\ProfileRepository $profileRepository */
        $profileRepository = $this->getDoctrine()->getRepository('AppTeamBundle:Profile');
        $profiles = $profileRepository->findAllActive();

        return $this->render('@AppTeam/Profile/index.html.twig', array(
            'page' => $cmsPage,
            'profiles' => $profiles,
        ), $response);
    }

    public function viewAction(Request $request, $slug, PageInterface $cmsPage)
    {
        /** @var ProfileRepository $profileRepo */
        $profileRepo = $this->getDoctrine()->getRepository('AppTeamBundle:Profile');

        /** @var Profile $profile */
        $profile = $profileRepo->findOneBySlugWithMenuNodes($slug);

        if (!$profile) {
            return $this->redirect($this->generateUrl('app_team.profile.index'), 301);
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        $seoPage = $this->get('vivo_site.seo.page');

        $seoPage->addTitlePart($profile->getName());

        return $this->render('@AppTeam/Profile/view.html.twig', array(
            'page' => $cmsPage,
            'profile' => $profile,
        ));
    }
}
