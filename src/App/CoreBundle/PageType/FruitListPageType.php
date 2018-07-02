<?php

namespace App\CoreBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class FruitListPageType extends AbstractPageType
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
        return 'Fruit List Page';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'fruits';
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
        return 'AppCoreBundle:Fruit:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'app_core.fruit.index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        $collection->add('app_core.fruit.view', $this->getRoute(
                array(
                    '_controller' => 'AppCoreBundle:Fruit:view',
                ), '/{slug}')
        );

        return $collection;
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
                    'banner',
                    'Banner Image', array(
                        'multiple' => true,
                        'note' => 'Only first image used',
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
}
