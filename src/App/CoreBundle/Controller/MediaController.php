<?php

namespace App\CoreBundle\Controller;

use App\CoreBundle\Entity\Contact;
use App\CoreBundle\Form\Type\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Vivo\AssetBundle\Model\FileInterface;
use Vivo\PageBundle\Event\Events;
use Vivo\PageBundle\Event\PrepareSeoEvent;
use Vivo\PageBundle\Model\PageInterface;

class MediaController extends Controller
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

        return $this->render('@AppCore/Media/index.html.twig', array(
            'page' => $cmsPage
        ), $response);
    }

    public function downloadFileAction($id,$name)
    {
        /** @var \Vivo\AssetBundle\Repository\FileRepositoryInterface $repository */
        $repository = $this->get('vivo_asset.repository.file');
        $file = $repository->findOneById($id);

        if (!$file) {
            throw $this->createNotFoundException(sprintf("Could not find file id '%s'", $id));
        }

        $repository->touch($file);

        return $this->getDownloadResponse($file, $name);
    }

    /**
     * @param  FileInterface    $file
     * @param $name
     * @return StreamedResponse
     */
    protected function getDownloadResponse(FileInterface $file, $name)
    {
        $response = new StreamedResponse(function () use ($file) {
            $handle = fopen($file->getAbsolutePath(), 'r');
            while (!feof($handle)) {
                $buffer = fread($handle, 1024);
                echo $buffer;
                flush();
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', $file->getMimeType());
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$name.'"');
        $response->headers->set('Content-Length', $file->getSize());

        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        $response->setEtag($file->getHash());
        $response->setLastModified($file->getUpdatedAt());

        return $response;
    }
}
