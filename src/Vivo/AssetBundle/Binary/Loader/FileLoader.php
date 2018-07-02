<?php

namespace Vivo\AssetBundle\Binary\Loader;

use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Binary\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vivo\AssetBundle\Model\FileInterface;
use Vivo\AssetBundle\Repository\FileRepositoryInterface;

class FileLoader implements LoaderInterface
{
    /**
     * @var FileRepositoryInterface
     */
    protected $fileRepository;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @var bool
     */
    private $createNonExistentImages;

    /**
     * Constructor.
     *
     * @param FileRepositoryInterface $fileRepository
     * @param ImagineInterface        $imagine
     * @param bool                    $createNonExistentImages
     */
    public function __construct(FileRepositoryInterface $fileRepository, ImagineInterface $imagine = null, $createNonExistentImages = false)
    {
        $this->fileRepository = $fileRepository;
        $this->imagine = $imagine;
        $this->createNonExistentImages = $createNonExistentImages;
    }

    /**
     * {@inheritdoc}
     */
    public function find($path)
    {
        $explodedPath = explode('/', $path);
        $id = null;
        $secret = null;

        if (5 === count($explodedPath)) {
            $id = $explodedPath[2];
            $secret = $explodedPath[3];
        }

        if (null === $id || null === $secret) {
            throw new NotFoundHttpException(sprintf('Invalid path for request: "%s"', $path));
        }

        /** @var FileInterface $file */
        $file = $this->fileRepository->findOneById($id);

        if (!$file) {
            throw new NotFoundHttpException(sprintf('Source image not found for request: "%s"', $path));
        }

        if ($path !== $file->getImagePreviewPath(pathinfo($path, PATHINFO_BASENAME))) {
            throw new NotFoundHttpException(sprintf('Invalid path for for request: "%s"', $path));
        }

        return stream_get_contents($this->getStreamFromImage($file));
    }

    /**
     * Return the stream resource for FileInterface.
     *
     * @param FileInterface $file
     *
     * @return resource
     */
    protected function getStreamFromImage(FileInterface $file)
    {
        if ($file->getTouchedAt() < new \DateTime('-1 day')) {
            // Only touch the file if it hasn't been touched today
            $this->fileRepository->touch($file);
        }

        if ($this->createNonExistentImages && null !== $this->imagine && !file_exists($file->getAbsolutePath())) {
            return $this->createNonExistingImage($file);
        }

        if (!file_exists($file->getAbsolutePath())) {
            throw new \Exception(sprintf("File '%s' could not be found", $file->getAbsolutePath()));
        }

        return fopen($file->getAbsolutePath(), 'r');
    }

    /**
     * Creates a placeholder image for a non existing image.
     *
     * @param FileInterface $file
     *
     * @return resource
     */
    protected function createNonExistingImage(FileInterface $file)
    {
        $size = new Box($file->getWidth(), $file->getHeight());
        $image = $this->imagine->create($size);
        $background = $image->palette()->color('#cccccc');
        $canvas = $this->imagine->create($size, $background);

        return fopen('data://text/plain;base64,'.base64_encode((string) $canvas), 'r');
    }
}
