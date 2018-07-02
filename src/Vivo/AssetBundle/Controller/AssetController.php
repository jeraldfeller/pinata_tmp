<?php

namespace Vivo\AssetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vivo\AssetBundle\Exception\NonReadableFileException;
use Vivo\AssetBundle\Form\DataTransformer\FileDataTransformer;
use Vivo\AssetBundle\Model\FileInterface;
use Vivo\AssetBundle\Twig\AssetPreviewExtension;
use Symfony\Component\Security\Csrf\CsrfToken;

/**
 * AssetController.
 */
class AssetController extends Controller
{
    /**
     * Download asset.
     */
    public function downloadFileAction($id, $hash, $name)
    {
        /** @var \Vivo\AssetBundle\Repository\FileRepositoryInterface $repository */
        $repository = $this->get('vivo_asset.repository.file');
        $file = $repository->findOneById($id);

        if (!$file) {
            throw $this->createNotFoundException(sprintf("Could not find file id '%s'", $id));
        }

        if ($this->container->getParameter('vivo_asset.check_download_context')) {
            if ($hash !== $file->getFilenameHash($name, 'download')) {
                throw $this->createNotFoundException(sprintf("Could not find file id '%s'", $id));
            }
        } else {
            // @DEPRECATED
            @trigger_error('Using deprecated hash method. Download should use the \'download\' context. To be removed from 4.0.', E_USER_DEPRECATED);
            /*
             * NOTE: If you set vivo_asset.check_download_context all hardcoded download
             *       links must be updated within database. Searching for asset/download
             *       should find the offending links.
             */

            if ($hash !== $file->getFilenameHash($name) && $hash !== $file->getFilenameHash($name, 'download')) {
                throw $this->createNotFoundException(sprintf("Could not find file id '%s'", $id));
            }
        }

        $repository->touch($file);

        return $this->getDownloadResponse($file, $name);
    }

    /**
     * Upload a file and return json response.
     */
    public function uploadAction(Request $request)
    {
        $uploadUrl = $this->generateUrl($request->attributes->get('_route'), array_merge($request->query->all(), $request->attributes->get('_route_params')));
        $uploadExpiry = $request->request->get('upload_expiry');
        $assetClass = $request->request->get('asset_class');
        $csrfToken = $request->request->get('csrf_token');

        /** @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface $csrfTokenManager */
        $csrfTokenManager = $this->get('security.csrf.token_manager');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken($uploadUrl.$uploadExpiry.$assetClass, $csrfToken))) {
            throw new AccessDeniedException();
        }

        $now = new \DateTime('now');
        if ($uploadExpiry < $now->getTimestamp()) {
            return new JsonResponse(array(
                'errors' => array('Upload session has timed out. Please refresh to upload.'),
            ));
        }

        try {
            $reflection = new \ReflectionClass($assetClass);
            /** @var \Vivo\AssetBundle\Model\AssetInterface $asset */
            $asset = $reflection->newInstance();
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'errors' => array('Could not initialise upload. Please refresh and try again.'),
            ));
        }

        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile instanceof UploadedFile) {
            return new JsonResponse(array(
                'errors' => array('Upload failed.'),
            ));
        }

        if (UPLOAD_ERR_OK !== $uploadedFile->getError()) {
            return new JsonResponse(array(
                'errors' => array($uploadedFile->getErrorMessage()),
            ));
        }

        try {
            /** @var \Vivo\AssetBundle\Factory\FileFactory $fileFactory */
            $fileFactory = $this->container->get('vivo_asset.factory.file');
            $transformer = new FileDataTransformer($fileFactory);
            $file = $transformer->reverseTransform($uploadedFile);
        } catch (NonReadableFileException $e) {
            return new JsonResponse(array(
                'errors' => array('Could not read uploaded file.'),
            ));
        }

        $asset->setFile($file);

        $em = $this->getDoctrine()->getManager();

        $file->touch();

        if (!$file->getId()) {
            $em->persist($file);
        }

        $em->flush();

        $preview = array(
            'image' => null,
            'link' => null,
            'class' => null,
        );

        if ($asset->getImagePreviewPath()) {
            $preview['image'] = $asset->getImagePreviewPath();
            $filterSet = 'asset_collection_thumb';

            $this->container
                ->get('liip_imagine.controller')
                ->filterAction(
                    $request,
                    $preview['image'],
                    'asset'
                );

            $cacheManager = $this->container->get('liip_imagine.cache.manager');
            $preview['link'] = $this->container->get('templating.helper.assets')->getUrl($cacheManager->getBrowserPath($preview['image'], 'asset'));
            $preview['class'] = 'fancybox';
        } else {
            $preview['image'] = AssetPreviewExtension::getIconPathFromFile($this->container->getParameter('kernel.root_dir'), $file);
            $filterSet = 'asset_collection_icon';
            $preview['link'] = $this->container->get('router')->generate($asset->getRouteName('download'), $asset->getRouteParameters('download'));
        }

        $this->container
            ->get('liip_imagine.controller')
            ->filterAction(
                $request,
                $preview['image'],
                $filterSet
            );

        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $preview['image'] = $this->container->get('templating.helper.assets')->getUrl($cacheManager->getBrowserPath($preview['image'], $filterSet));

        /** @var \Vivo\UtilBundle\Util\EntitySignerUtil $entitySigner */
        $entitySigner = $this->get('vivo_util.util.entity_signer');

        return new JsonResponse(array(
            'errors' => null,
            'preview' => $preview,
            'asset' => array(
                'fileIdSigned' => $entitySigner->getSignedId($asset->getFile()),
                'fileId' => $asset->getFile()->getId(),
                'filename' => $asset->getFilename(),
                'title' => $asset->getTitle(),
            ),
        ));
    }

    /**
     * @param FileInterface $file
     * @param string        $name
     *
     * @return Response
     *
     * @throws NotFoundHttpException if file is not found
     */
    protected function getDownloadResponse(FileInterface $file, $name)
    {
        if (true !== file_exists($file->getAbsolutePath())) {
            throw new NotFoundHttpException(sprintf("'%s' does not exist.", $file->getAbsolutePath()));
        }

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
