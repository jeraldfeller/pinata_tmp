<?php

namespace Vivo\PageBundle\PageType\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;

class StandardPageType extends AbstractPageType
{
    /**
     * @var ArrayCollection
     */
    private $blocks;

    /**
     * @var bool
     */
    protected $enabled;

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
     * @param bool  $enabled
     * @param array $contentClass
     * @param array $assetGroupClass
     */
    public function __construct($enabled, array $contentBlocks, array $assetGroupBlocks)
    {
        $this->enabled = $enabled;
        $this->contentBlocks = $contentBlocks;
        $this->assetGroupBlocks = $assetGroupBlocks;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function isDefault()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Standard Page';
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'standard';
    }

    /**
     * {@inheritdoc}
     */
    public function isUnique()
    {
        return false;
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
}
