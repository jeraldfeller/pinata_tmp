<?php

namespace App\CoreBundle\Controller;

use App\CoreBundle\Entity\Farm;
use App\CoreBundle\Repository\FarmLocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\Page;
use Vivo\PageBundle\Model\PageInterface;
use App\CoreBundle\Repository\FarmRepository;

class FarmController extends Controller
{
    public function indexAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date

        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var FarmRepository $farmRepo */
        $farmRepo = $this->get('app_core.repository.farm');

        $pinatFarms = $farmRepo->findAllPinataFarms();

        $thirdPartyfarms = $farmRepo->findAllThirdPartyFarms();

        $allFarms = $farmRepo->findAllForListPage();

        /** @var FarmLocationRepository $farmRepo */
        $locationRepo = $this->get('app_core.repository.farm_location');

        $locations = $locationRepo->findAllForListPage();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        return $this->render('@AppCore/Farm/index.html.twig', array(
            'page' => $cmsPage,
            'farms' => $pinatFarms,
            'locations' => $locations,
            'thirdParty' => $thirdPartyfarms,
            'allFarms' => $allFarms,
        ), $response);
        
        if (!$farm) {
            return $this->redirect($this->generateUrl('app_core.farm.index'), 301);
        }
    }

    public function viewAction(Request $request, $slug, PageInterface $cmsPage)
    {
        /** @var FarmRepository $farmRepo */
        $farmRepo = $this->get('app_core.repository.farm');

        /** @var Farm $farm */
        $farm = $farmRepo->findOneBySlugWithMenuNodes($slug);

        if (!$farm) {
            return $this->redirect($this->generateUrl('app_core.farm.index'), 301);
        }
        
        $next_url = $pre_url = null;
        /** @var Farm $nextfarm*/
        $nextfarm = $farmRepo->findNextBySlugWithMenuNodes($farm->getRank());
        if($nextfarm){
         $next_url = $nextfarm->getPrimarySlug()->getSlug();
        }
        
        /** @var Farm $nextfarm*/
        $prefarm = $farmRepo->findPreBySlugWithMenuNodes($farm->getRank());
        if($prefarm){
         $pre_url = $prefarm->getPrimarySlug()->getSlug();
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        $seoPage = $this->get('vivo_site.seo.page');

        $seoPage->addTitlePart($farm->getName());

        return $this->render('@AppCore/Farm/view.html.twig', array(
            'page' => $cmsPage,
            'farm' => $farm,
            'farm_links' => array('pre_url'=>$pre_url, 'next_url'=>$next_url)
        ));
    }
}
