<?php

namespace Vivo\AssetBundle\Model;

use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

/**
 * FileInterface.
 */
interface FileInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get hash.
     *
     * @return string
     */
    public function getHash();

    /**
     * Get extension.
     *
     * @return string
     */
    public function getExtension();

    /**
     * Get mimeType.
     *
     * @return string
     */
    public function getMimeType();

    /**
     * Get size.
     *
     * @return int
     */
    public function getSize();

    /**
     * Get width.
     *
     * @return int
     */
    public function getWidth();

    /**
     * Get height.
     *
     * @return int
     */
    public function getHeight();

    /**
     * Set touchedAt.
     *
     * @return $this
     */
    public function touch();

    /**
     * Get touchedAt.
     *
     * @return \DateTime
     */
    public function getTouchedAt();

    /**
     * Set salt.
     *
     * @param string $salt
     *
     * @return $this
     */
    public function setSalt($salt);

    /**
     * Get salt.
     *
     * @return string
     */
    public function getSalt();

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Set updatedAt.
     *
     * @return \DateTime
     */
    public function setUpdatedAt(\DateTime $updatedAt);

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * Add assets.
     *
     * @param \Vivo\AssetBundle\Model\AssetInterface $asset
     *
     * @return $this
     */
    public function addAsset(AssetInterface $asset);

    /**
     * Remove assets.
     *
     * @param \Vivo\AssetBundle\Model\AssetInterface $asset
     */
    public function removeAsset(AssetInterface $asset);

    /**
     * Get assets.
     *
     * @return \Vivo\AssetBundle\Model\AssetInterface[]
     */
    public function getAssets();

    /**
     * @param string $filename
     * @param string $context
     *
     * @return string
     */
    public function getFilenameHash($filename, $context = null);

    /**
     * Return the size in a formatted string.
     *
     * @param int $precision
     *
     * @return string
     */
    public function getFormattedSize($precision = 2);

    /**
     * Set file.
     *
     * @param SymfonyFile $file
     *
     * @return mixed
     */
    public function setFile(SymfonyFile $file);

    /**
     * Returns the file that was uploaded.
     *
     * @return SymfonyFile|null
     */
    public function getFile();

    /**
     * Get image mime types.
     *
     * @return array
     */
    public static function getImageMimeTypes();

    /**
     * Return true if this file is an image.
     *
     * @return bool
     */
    public function isImage();

    /**
     * Return the preview path for this file (eg first frame of video).
     *
     * @param string $filename
     *
     * @return string|null
     */
    public function getImagePreviewPath($filename);

    /**
     * Return route name.
     *
     * @param AssetInterface $asset
     * @param $context
     *
     * @return string
     *
     * @throws \Exception
     */
    public function getRouteName(AssetInterface $asset, $context);

    /**
     * Return route parameters.
     *
     * @param AssetInterface $asset
     * @param $context
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRouteParameters(AssetInterface $asset, $context);

    /**
     * Returns the absolute path to the file.
     *
     * @return string
     */
    public function getAbsolutePath();

    /**
     * Returns the relative path to the file.
     *
     * @return string
     */
    public function getPath();

    /**
     * Set uploadDirectory.
     *
     * @param $uploadDirectory
     *
     * @return mixed
     */
    public function setUploadDirectory($uploadDirectory);
}
