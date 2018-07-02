<?php

namespace Vivo\AssetBundle\Twig;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Vivo\AssetBundle\Model\AssetInterface;
use Vivo\AssetBundle\Model\FileInterface;

class AssetPreviewExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $kernelRootDir;

    /**
     * @var \Liip\ImagineBundle\Imagine\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * Constructor.
     *
     * @param CacheManager $cacheManager
     * @param $kernelRootDir
     */
    public function __construct(CacheManager $cacheManager, $kernelRootDir)
    {
        $this->cacheManager = $cacheManager;
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('vivo_asset_preview', array($this, 'preview')),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'vivo_asset_preview';
    }

    /**
     * @param AssetInterface $file
     * @param string         $imageFilter
     * @param string         $iconFilter
     *
     * @return string
     */
    public function preview(AssetInterface $asset, $filter, $iconFilter)
    {
        if (null === $path = $asset->getImagePreviewPath()) {
            $path = self::getIconPathFromFile($this->kernelRootDir, $asset->getFile());
            $filter = $iconFilter;
        }

        return new \Twig_Markup(
            $this->cacheManager->getBrowserPath($path, $filter),
            'utf8'
        );
    }

    /**
     * @param $kernelRootDir
     * @param FileInterface $file
     *
     * @return string
     */
    public static function getIconPathFromFile($kernelRootDir, FileInterface $file)
    {
        $ext = strtolower($file->getExtension());

        switch ($ext) {
            case 'jpeg':
                $ext = 'jpg';
                break;

            case 'xls':
                $ext = 'xlsx';
                break;

            case 'csv':
                $ext = 'txt';
                break;

            case 'ppt':
                $ext = 'pptx';
                break;

            case 'doc':
                $ext = 'docx';
                break;
        }

        $iconPath = 'bundles/vivoasset/img/icon/';
        $iconPathAbs = $kernelRootDir.'/../web/'.$iconPath;

        if (file_exists($iconPathAbs.$ext.'.png')) {
            return $iconPath.$ext.'.png';
        } else {
            $mimeExt = null;

            switch (strtolower($file->getMimeType())) {
                case 'text/plain':
                    $mimeExt = 'txt';
                    break;
            }

            if ($mimeExt && file_exists($iconPathAbs.$mimeExt.'.png')) {
                return $iconPath.$mimeExt.'.png';
            }
        }

        return $iconPath.'blank.png';
    }
}
