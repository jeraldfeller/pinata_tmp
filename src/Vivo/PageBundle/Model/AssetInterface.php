<?php

namespace Vivo\PageBundle\Model;

use Vivo\AssetBundle\Model\AssetInterface as BaseAssetInterface;

/**
 * AssetInterface.
 */
interface AssetInterface extends BaseAssetInterface
{
    /**
     * Set asset collection.
     *
     * @param \Vivo\PageBundle\Model\AssetGroupInterface $assetGroup
     *
     * @return $this
     */
    public function setAssetGroup(AssetGroupInterface $assetGroup = null);

    /**
     * Get asset collection.
     *
     * @return \Vivo\PageBundle\Model\AssetGroupInterface
     */
    public function getAssetGroup();
}
