<?php

namespace Vivo\AssetBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vivo\AssetBundle\Factory\FileFactoryInterface;
use Vivo\AssetBundle\Exception\NonReadableFileException;

class FileDataTransformer implements DataTransformerInterface
{
    /**
     * @var \Vivo\AssetBundle\Factory\FileFactoryInterface
     */
    protected $fileFactory;

    /**
     * @param FileFactoryInterface $fileFactory
     */
    public function __construct(FileFactoryInterface $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($file)
    {
        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($uploadedFile)
    {
        if (null === $uploadedFile || !$uploadedFile instanceof UploadedFile) {
            return;
        }

        if (UPLOAD_ERR_OK !== $uploadedFile->getError()) {
            throw new TransformationFailedException($uploadedFile->getErrorMessage());
        }

        try {
            return $this->fileFactory->findOneFileOrCreate($uploadedFile);
        } catch (NonReadableFileException $e) {
            throw new TransformationFailedException();
        }
    }
}
