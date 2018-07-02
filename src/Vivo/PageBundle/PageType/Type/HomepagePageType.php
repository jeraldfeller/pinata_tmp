<?php

namespace Vivo\PageBundle\PageType\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\AssetGroupBlock;
use Vivo\PageBundle\PageType\Block\ContentBlock;

class HomepagePageType extends AbstractPageType
{
    const ALIAS = 'homepage';

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
     * @param array $contentBlocks
     * @param array $assetGroupBlocks
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
    public function isAlwaysEnabled()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Homepage';
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
    public function getSlug()
    {
        return '/';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'homepage';
    }

    /**
     * {@inheritdoc}
     */
    public function getController()
    {
        return 'VivoPageBundle:Page:homepage';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return 'Vivo\PageBundle\Form\Type\HomepagePageType';
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationGroups()
    {
        return [
            'Default',
            'validate_page_type_'.$this->getAlias(),
        ];
    }
}
