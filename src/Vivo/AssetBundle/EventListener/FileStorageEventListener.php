<?php

namespace Vivo\AssetBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Vivo\AssetBundle\Model\FileInterface;

/**
 * FileStorageEventListener.
 *
 * This listener handles the storage of the files
 */
class FileStorageEventListener
{
    /**
     * @var string
     */
    protected $uploadDirectory;

    /**
     * @var string
     */
    protected $deleteFile;

    /**
     * @param string $uploadDirectory
     */
    public function __construct($uploadDirectory)
    {
        $this->uploadDirectory = $uploadDirectory;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof FileInterface) {
            $entity->setUploadDirectory($this->uploadDirectory);
        }
    }

    /**
     * Store the uploaded file.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof FileInterface) {
            if ($entity->getFile()) {
                $entity->getFile()->move(dirname($entity->getAbsolutePath()), pathinfo($entity->getAbsolutePath(), PATHINFO_FILENAME));
            }
        }
    }

    /**
     * If the file does not exist store it.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof FileInterface) {
            if ($entity->getFile()) {
                $directory = dirname($entity->getAbsolutePath());
                $filename = pathinfo($entity->getAbsolutePath(), PATHINFO_FILENAME);

                if (!file_exists($directory.'/'.$filename)) {
                    $entity->getFile()->move($directory, $filename);
                }
            }
        }
    }

    /**
     * Store the file to be deleted from file system.
     *
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof FileInterface) {
            $this->deleteFile = $entity->getAbsolutePath();
        }
    }

    /**
     * Delete the file from storage.
     *
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        if (null !== $this->deleteFile) {
            unlink($this->deleteFile);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof FileInterface) {
            $entity->setUploadDirectory($this->uploadDirectory);
        }
    }
}
