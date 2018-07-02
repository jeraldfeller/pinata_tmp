<?php

namespace App\CoreBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class FarmListPageType extends AbstractPageType
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
        return 'Farm List Page';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'farms';
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
        return 'AppCoreBundle:Farm:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'app_core.farm.index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        $collection->add('app_core.farm.view', $this->getRoute(
                array(
                    '_controller' => 'AppCoreBundle:Farm:view',
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
            ));
        }

        return $this->blocks;
    }
}
