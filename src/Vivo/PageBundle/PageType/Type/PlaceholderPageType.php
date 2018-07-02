<?php

namespace Vivo\PageBundle\PageType\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;

class PlaceholderPageType extends AbstractPageType
{
    const ALIAS = 'placeholder';

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
    public function getName()
    {
        return 'Menu Placeholder';
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
    public function isUnique()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return 'Vivo\PageBundle\Form\Type\PlaceholderPageType';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return 'VivoPageBundle:Page:placeholder';
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationGroups()
    {
        $groups = parent::getValidationGroups();
        $groups[] = 'RequiresMenuNode';

        return $groups;
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
