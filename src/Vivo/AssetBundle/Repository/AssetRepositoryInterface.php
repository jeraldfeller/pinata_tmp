<?php

namespace Vivo\AssetBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\AssetBundle\Model\AssetInterface;

/**
 * AssetRepositoryInterface.
 */
interface AssetRepositoryInterface extends ObjectRepository
{
    /**
     * @param $id
     *
     * @return \Vivo\AssetBundle\Model\AssetInterface|null
     */
    public function findOneById($id);

    /**
     * @param AssetInterface $asset
     *
     * @return \Vivo\AssetBundle\Model\AssetInterface|null
     */
    public function findDuplicateAssetByFilename(AssetInterface $asset);

    /**
     * Creates a new instance of AssetInterface.
     *
     * @return \Vivo\AssetBundle\Model\AssetInterface
     */
    public function createAsset();
}
