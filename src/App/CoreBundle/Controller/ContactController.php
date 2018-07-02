<?php

namespace App\CoreBundle\Controller;

use App\CoreBundle\Entity\Contact;
use App\CoreBundle\Form\Type\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;

class ContactController extends Controller
{
    public function indexAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();

        $contact = new Contact();

        $form = $this->createForm(new ContactType(), $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $request->isXmlHttpRequest()) {
            $jsonResponse = array();
            if ($form->isValid()) {

                /** @var \App\CoreBundle\Util\Mailer $mailer */
                $mailer = $this->get('app_core.util.mailer');

                $contact
                    ->setIpAddress($request->getClientIp())
                    ->setHttpUserAgent($request->server->get('HTTP_USER_AGENT'));

                $em = $this->getDoctrine()->getManager();
                $em->persist($contact);
                $em->flush();

                $mailer->sendContactEmail($contact);

                $jsonResponse['redirect'] = $this->generateUrl('app_core.contact.thankyou');
                $jsonResponse['gaq'] = array(
                    'category' => 'contact',
                    'action' => 'submit',
                );
            } else {
                $jsonResponse['payload'] = $this->renderView('@AppCore/Contact/_form.html.twig', array(
                    'form' => $form->createView(),
                ));
            }

            return new JsonResponse($jsonResponse);
        }

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        return $this->render('@AppCore/Contact/index.html.twig', array(
            'page' => $cmsPage,
            'form' => $form->createView(),
        ), $response);
    }

    public function thankyouAction(Request $request, PageInterface $cmsPage)
    {
        $response = new Response();
        $response->setSharedMaxAge(10); // Number of seconds to check headers for last modified date
        if ($response->isNotModified($request)) {
            return $response;
        }

        $cmsPage->setRobotsNoIndex(true);

        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('event_dispatcher');
        $eventDispatcher->dispatch(Events::PREPARE_SEO, new PrepareSeoEvent($cmsPage, $request));

        return $this->render('@AppCore/Contact/thankyou.html.twig', array(
            'page' => $cmsPage,
        ), $response);
    }
}
