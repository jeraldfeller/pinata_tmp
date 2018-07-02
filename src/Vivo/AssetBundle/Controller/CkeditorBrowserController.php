<?php

namespace Vivo\AssetBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\AssetBundle\Form\DataTransformer\FileDataTransformer;
use Vivo\AssetBundle\Model\AssetInterface;

/**
 * CkeditorBrowserController.
 */
class CkeditorBrowserController extends Controller
{
    /**
     * Quick Upload.
     */
    public function quickUploadAction(Request $request, $type)
    {
        /** @var \Vivo\AssetBundle\Repository\AssetRepositoryInterface $repository */
        $repository = $this->get('vivo_asset.repository.ckeditor_asset');
        /** @var \Symfony\Component\Validator\Validator $validator */
        $validator = $this->get('validator');

        $asset = null;
        $error = null;

        if (($uploadedFile = $request->files->get('upload')) instanceof UploadedFile) {
            $fileDataTransformer = new FileDataTransformer($this->get('vivo_asset.factory.file'));

            $asset = $repository->createAsset();
            $asset->setFile($fileDataTransformer->reverseTransform($uploadedFile));
        }

        if ($asset) {
            if ('image' === $type && !$asset->getFile()->isImage()) {
                $error = 'Invalid file type.';
            }

            $errors = $validator->validate($asset);
            if (count($errors) > 0) {
                $asset = null;

                $error = '';
                foreach ($errors as $e) {
                    $error .= $e->getMessage().'\n';
                }
            } else {
                $this->persistAssetOrUpdateDuplicate($asset);
            }
        } else {
            $error = 'File upload failed.';
        }

        return $this->render('@VivoAsset/CkeditorBrowser/quick_upload.html.twig', array(
            'type' => $type,
            'asset' => $asset,
            'resolved_path' => $this->getResolvedPath($asset),
            'error' => $error,
        ));
    }

    /**
     * Display Ckeditor Assets.
     */
    public function indexAction(Request $request, $type)
    {
        $fileTypes = $this->getFileTypes();

        if (!array_key_exists($type, $fileTypes)) {
            throw $this->createNotFoundException(sprintf("'%s' is not a valid file type.", $type));
        }

        $selectedFileType = $fileTypes[$type];

        /** @var \Vivo\AssetBundle\Repository\AssetRepositoryInterface $repository */
        $repository = $this->get('vivo_asset.repository.ckeditor_asset');
        $paginator = $this->get('knp_paginator');

        $queryBuilder = $repository->createQueryBuilder('asset')
            ->addSelect('file')
            ->leftJoin('asset.file', 'file')
            ->orderBy('asset.updatedAt', 'DESC');

        $queryBuilder = $selectedFileType['query_builder']($queryBuilder);

        $asset = $repository->createAsset();

        $options = array();

        $mode = $request->query->get('mode', $type);

        if ('image' === $request->query->get('mode', $type)) {
            $options = array(
                'file_options' => array(
                    'attr' => array(
                        'accept' => 'image/*',
                    ),
                ),
            );
        }

        $uploadForm = $this->createForm('Vivo\AssetBundle\Form\Type\AssetUploadType', $asset, $options);
        $uploadForm->handleRequest($request);

        if ($uploadForm->isValid()) {
            $this->persistAssetOrUpdateDuplicate($asset);

            $this->get('session')->getFlashBag()->add('success', 'File successfully uploaded.');

            $uploadedType = null;
            foreach ($this->getFileTypes() as $key => $val) {
                $supported = $val['supported']($asset);

                if (null === $supported) {
                    $uploadedType = $key;
                } elseif (true === $supported) {
                    $uploadedType = $key;
                    break;
                }
            }

            return $this->redirectToRoute('vivo_asset.ckeditor_browser.index', array_merge(
                $request->query->all(),
                array(
                    'type' => $uploadedType,
                    'page' => 1,
                )
            ));
        }

        $searchList = $this->getSearchList();
        $searchForm = $this->createForm('Vivo\UtilBundle\Form\Type\SearchListType', $searchList);

        /** @var \Vivo\AssetBundle\Model\AssetInterface[] $pagination */
        $pagination = $paginator->paginate($searchList->getSearchQueryBuilder($queryBuilder), $request->query->get('page', 1), 25);

        $resolvedPaths = array();
        if ('image' === $mode) {
            foreach ($pagination as $asset) {
                if (null !== $resolvedPath = $this->getResolvedPath($asset)) {
                    $resolvedPaths[$asset->getId()] = $resolvedPath;
                }
            }
        }

        return $this->get('vivo_util.util.ajax_response')->getResponse(
            $request,
            '@VivoAsset/CkeditorBrowser/index.html.twig',
            array(
                'mode' => $mode,
                'uploadForm' => $uploadForm->createView(),
                'asset_types' => $fileTypes,
                'asset_type' => $type,
                'searchForm' => $searchForm->handleRequest($request)->createView(),
                'pagination' => $pagination,
                'resolved_paths' => $resolvedPaths,
            )
        );
    }

    /**
     * Deletes CkeditorAsset entity.
     */
    public function deleteAction(Request $request, $id, $token)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var \Vivo\AssetBundle\Repository\AssetRepositoryInterface $repository */
        $repository = $this->get('vivo_asset.repository.ckeditor_asset');
        $asset = $repository->findOneById($id);

        if (!$asset) {
            throw $this->createNotFoundException('Unable to find Asset entity.');
        }

        if (true !== $this->isCsrfTokenValid($asset->getCsrfIntention('delete'), $token)) {
            throw new InvalidCsrfTokenException('Invalid csrf token.');
        } else {
            $em->remove($asset);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'File successfully deleted.');
        }

        if (false !== $referrer = $request->server->get('HTTP_REFERER', false)) {
            return $this->redirect($referrer);
        }

        return $this->redirectToRoute('admin_homepage');
    }

    protected function persistAssetOrUpdateDuplicate(AssetInterface $asset)
    {
        /** @var \Vivo\AssetBundle\Repository\AssetRepositoryInterface $repository */
        $repository = $this->get('vivo_asset.repository.ckeditor_asset');

        $em = $this->getDoctrine()->getManager();

        if ($duplicate = $repository->findDuplicateAssetByFilename($asset)) {
            $duplicate->setUpdatedAt(new \DateTime('now'));

            $em->persist($duplicate);
        } else {
            $em->persist($asset);
        }

        $em->flush();
    }

    protected function getFileTypes()
    {
        $imageMimeTypes = call_user_func($this->container->getParameter('vivo_asset.model.file').'::getImageMimeTypes');

        return [
            'all' => array(
                'name' => 'All',
                'query_builder' => function (QueryBuilder $qb) use ($imageMimeTypes) {
                    return $qb;
                },
                'supported' => function (AssetInterface $asset) {
                    return;
                },
            ),
            'image' => array(
                'name' => 'Images',
                'query_builder' => function (QueryBuilder $qb) use ($imageMimeTypes) {
                    return $qb->andWhere('(file.mimeType in (:mime_types) and file.width IS NOT NULL and file.height IS NOT NULL)')
                        ->setParameter('mime_types', $imageMimeTypes)
                    ;
                },
                'supported' => function (AssetInterface $asset) {
                    return $asset->getFile()->isImage() ? true : false;
                },
            ),
            'file' => array(
                'name' => 'Files',
                'query_builder' => function (QueryBuilder $qb) use ($imageMimeTypes) {
                    return $qb->andWhere('(file.mimeType not in (:mime_types) or file.width IS NULL or file.height IS NULL)')
                        ->setParameter('mime_types', $imageMimeTypes)
                    ;
                },
                'supported' => function (AssetInterface $asset) {
                    return $asset->getFile()->isImage() ? false : true;
                },
            ),
        ];
    }

    /**
     * @param null $context
     *
     * @return SearchList
     */
    protected function getSearchList($context = null)
    {
        $searchList = new SearchList();
        $searchList->setEqualColumns(array('asset.id'))
            ->setLikeColumns(array('asset.filename'));

        return $searchList;
    }

    /**
     * Get resolved path.
     *
     * @param AssetInterface $asset
     *
     * @return null|string
     */
    protected function getResolvedPath(AssetInterface $asset)
    {
        if (!$asset->getFile()->isImage() || !($path = $asset->getImagePreviewPath())) {
            return;
        }

        /** @var \Liip\ImagineBundle\Imagine\Cache\CacheManager $cacheManager */
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        $filter = 'content';

        if (!$cacheManager->isStored($path, $filter)) {
            return $cacheManager->resolve($path, $filter);
        }

        return;
    }
}
