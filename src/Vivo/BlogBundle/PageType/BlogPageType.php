<?php

namespace Vivo\BlogBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class BlogPageType extends AbstractPageType
{
    const ALIAS = 'blog';

    /**
     * @var ArrayCollection
     */
    private $blocks;

    /**
     * @var array
     */
    protected $contentBlocks;

    /**
     * @var array
     */
    protected $assetGroupBlocks;

    /**
     * Constructor.
     *
     * @param array $contentBlocks
     * @param array $assetGroupBlocks
     */
    public function __construct(array $contentBlocks, array $assetGroupBlocks)
    {
        $this->contentBlocks = $contentBlocks;
        $this->assetGroupBlocks = $assetGroupBlocks;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Blog List Page';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return self::ALIAS;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlocks()
    {
        if (null === $this->blocks) {
            $this->blocks = new ArrayCollection();

            foreach ($this->contentBlocks as $alias => $block) {
                $this->blocks->add(new ContentBlock($block['class'], $alias, $block['name'], $block['options'], $block['form_type']));
            }

            foreach ($this->assetGroupBlocks as $alias => $block) {
                $this->blocks->add(new AssetGroupBlock($block['class'], $alias, $block['name'], $block['options'], $block['form_type']));
            }
        }

        return $this->blocks;
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
        return 'VivoBlogBundle:Post:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'vivo_blog.post.index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteCollection()
    {
        $collection = parent::getRouteCollection();

        $collection->add('vivo_blog.post.view', $this->getRoute(array(
            '_controller' => 'VivoBlogBundle:Post:view',
        ), '/{year}/{month}/{day}/{slug}')->setRequirements(array(
            'year' => '\d{4}',
            'month' => '0[1-9]|1[0-2]',
            'day' => '0[1-9]|1[0-9]|2[0-9]|3[0-1]',
        )));

        $collection->add('vivo_blog.archive.index', $this->getRoute(array(
            '_controller' => 'VivoBlogBundle:Archive:index',
        ), '/{year}/{month}')->setRequirements(array(
            'year' => '\d{4}',
            'month' => '0[1-9]|1[0-2]',
        )));

        $collection->add('vivo_blog.category.index', $this->getRoute(array(
            '_controller' => 'VivoBlogBundle:Category:index',
        ), '/{slug}'));

        $collection->add('vivo_blog.category.view', $this->getRoute(array(
            '_controller' => 'VivoBlogBundle:Category:view',
        ), '/{category_slug}/{post_slug}'));

        return $collection;
    }
}
