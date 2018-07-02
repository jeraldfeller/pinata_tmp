<?php

namespace App\CoreBundle\Controller;

use App\CoreBundle\Entity\Timeline;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TimelineController extends Controller
{
    public function timelineAction(Request $request)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date

        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var TimelineRepository $timelineRepo */
        $timelineRepo = $this->get('app_core.repository.timeline');

        $items = $timelineRepo->findAllForListPage();

        return $this->render('@AppCore/Timeline/index.html.twig', array(
            'items' => $items,
        ), $response);
    }
}
