<?php

namespace Vivo\PageBundle\Model;

interface AssetGroupInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set alias.
     *
     * @param $alias
     *
     * @return $this
     */
    public function setAlias($alias);

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Set page.
     *
     * @param PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null);

    /**
     * Get page.
     *
     * @return \Vivo\PageBundle\Model\PageInterface
     */
    public function getPage();

    /**
     * Add asset.
     *
     * @param \Vivo\AssetBundle\Model\AssetInterface $asset
     *
     * @return $this
     */
    public function addAsset(AssetInterface $asset);

    /**
     * Remove asset.
     *
     * @param \Vivo\AssetBundle\Model\AssetInterface $asset
     */
    public function removeAsset(AssetInterface $asset);

    /**
     * Get assets.
     *
     * @return \Vivo\PageBundle\Model\AssetInterface[]
     */
    public function getAssets();

    /**
     * Get active assets.
     *
     * @return \Vivo\PageBundle\Model\AssetInterface[]
     */
    public function getActiveAssets();

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
