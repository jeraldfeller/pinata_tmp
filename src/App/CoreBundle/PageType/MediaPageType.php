<?php

namespace App\CoreBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class MediaPageType extends AbstractPageType
{
    /**
     * @var ArrayCollection
     */
    private $blocks;

    /**
     * @var string
     */
    protected $contentClass;

    /**
     * @var string
     */
    protected $assetGroupClass;

    /**
     * Constructor.
     *
     * @param string $contentClass
     * @param string $assetGroupClass
     */
    public function __construct($contentClass, $assetGroupClass)
    {
        $this->contentClass = $contentClass;
        $this->assetGroupClass = $assetGroupClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Media Downloads Page Layout';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'media';
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return 'AppCoreBundle:Media:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        if (null === $this->blocks) {
            $this->blocks = new ArrayCollection(array(
                new ContentBlock($this->contentClass, 'main', 'Main Content'),
                new AssetGroupBlock(
                    $this->assetGroupClass,
                    'logos',
                    'Logos', array(
                    'multiple' => true,
                    'note' => 'PDF Logos',
                    'options' => array(
                        'linkable' => false,
                    ),
                ),
                    'vivo_asset_asset_file_collection'
                ),
                new AssetGroupBlock(
                    $this->assetGroupClass,
                    'images',
                    'Promotional Images', array(
                    'multiple' => true,
                    'note' => 'Upload images any size. All images will be linked to and thumbnails automatically generated.',
                    'options' => array(
                        'linkable' => false,
                    ),
                ),
                    'vivo_asset_asset_image_collection'
                ),
            ));
        }

        return $this->blocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        $collection->add('app_core.media.index', $this->getRoute());

        return $collection;
    }
}
