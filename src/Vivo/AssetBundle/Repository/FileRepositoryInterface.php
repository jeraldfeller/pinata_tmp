<?php

namespace Vivo\AssetBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\AssetBundle\Model\FileInterface;

/**
 * FileRepositoryInterface.
 */
interface FileRepositoryInterface extends ObjectRepository
{
    /**
     * @param int $id
     *
     * @return \Vivo\AssetBundle\Model\FileInterface|null
     */
    public function findOneById($id);

    /**
     * Find a file already matching the hash.
     *
     * @param string $hash
     *
     * @return \Vivo\AssetBundle\Model\FileInterface|null
     */
    public function findOneFileByHash($hash);

    /**
     * Creates a new instance of FileInterface.
     *
     * @return \Vivo\AssetBundle\Model\FileInterface
     */
    public function createFile();

    /**
     * Touch file.
     *
     * @param FileInterface $file
     * @param bool          $andFlush
     */
    public function touch(FileInterface $file, $andFlush = true);
}
