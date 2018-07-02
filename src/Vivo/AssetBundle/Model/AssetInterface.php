<?php

namespace Vivo\AssetBundle\Model;

/**
 * AssetInterface.
 */
interface AssetInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return $this
     */
    public function setFilename($filename);

    /**
     * Get filename.
     *
     * @param bool $clean
     *
     * @return string
     */
    public function getFilename($clean = false);

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set link.
     *
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link);

    /**
     * Get link.
     *
     * @return string
     */
    public function getLink();

    /**
     * Set linkTarget.
     *
     * @param string $linkTarget
     *
     * @return $this
     */
    public function setLinkTarget($linkTarget);

    /**
     * Get linkTarget.
     *
     * @return string
     */
    public function getLinkTarget();

    /**
     * Set activeAt.
     *
     * @param \DateTime $activeAt
     *
     * @return $this
     */
    public function setActiveAt(\DateTime $activeAt = null);

    /**
     * Get activeAt.
     *
     * @return \DateTime
     */
    public function getActiveAt();

    /**
     * Set expiresAt.
     *
     * @param \DateTime $expiresAt
     *
     * @return $this
     */
    public function setExpiresAt(\DateTime $expiresAt = null);

    /**
     * Get expiresAt.
     *
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * Return true if the asset is active.
     *
     * @return bool
     */
    public function isActive();

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return $this
     */
    public function setRank($rank);

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank();

    /**
     * Set file.
     *
     * @param \Vivo\AssetBundle\Model\FileInterface $file
     *
     * @return $this
     */
    public function setFile(FileInterface $file);

    /**
     * Get file.
     *
     * @return \Vivo\AssetBundle\Model\FileInterface
     */
    public function getFile();

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Return the preview path for this file (eg first frame of video).
     *
     * @return null|string
     */
    public function getImagePreviewPath();

    /**
     * Return route name.
     *
     * @param $context
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getRouteName($context);

    /**
     * Return route parameters.
     *
     * @param $context
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRouteParameters($context);

    /**
     * Set alt.
     *
     * @param string $alt
     *
     * @return $this
     */
    public function setAlt($alt);

    /**
     * Get alt.
     *
     * @return string
     */
    public function getAlt();
}
