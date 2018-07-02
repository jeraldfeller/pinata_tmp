<?php

namespace App\TeamBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class ProfilePageType extends AbstractPageType
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
        return 'Team Profile List';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'app_team_profile';
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return 'AppTeamBundle:Profile:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'app_team.profile.index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        $collection->add('app_team.profile.view', $this->getRoute(
                array(
                    '_controller' => 'AppTeamBundle:Profile:view',
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
                /*
                new AssetGroupBlock(
                    $this->assetGroupClass,
                    'main',
                    'Header Image', array(
                        'note' => 'Only the first image will be displayed.',
                        'options' => array(
                            'linkable' => true,
                        )
                    ),
                    'vivo_asset_asset_image_collection'
                ),
                */
            ));
        }

        return $this->blocks;
    }
}
