<?php

namespace Vivo\PageBundle\Model;

use Vivo\AssetBundle\Model\Asset as BaseAsset;

/**
 * Asset.
 */
class Asset extends BaseAsset implements AssetInterface
{
    /**
     * @var AssetInterface
     */
    protected $assetGroup;

    /**
     * {@inheritdoc}
     */
    public function setAssetGroup(AssetGroupInterface $assetGroup = null)
    {
        $this->assetGroup = $assetGroup;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAssetGroup()
    {
        return $this->assetGroup;
    }

    /**
     * Get models to cascade the updated timestamp to.
     *
     * @return \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait[]
     */
    public function getCascadedTimestampableFields()
    {
        return array($this->getAssetGroup());
    }
}
