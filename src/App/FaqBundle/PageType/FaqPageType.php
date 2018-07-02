<?php

namespace App\FaqBundle\PageType;

use Doctrine\Common\Collections\ArrayCollection;
use Vivo\PageBundle\PageType\Block\ContentBlock;
use Vivo\PageBundle\PageType\Type\AbstractPageType;

class FaqPageType extends AbstractPageType
{
    const ALIAS = 'faq';

    /**
     * @var ArrayCollection
     */
    private $blocks;

    /**
     * @var string
     */
    private $contentClass;

    /**
     * Constructor.
     *
     * @param string $contentClass
     */
    public function __construct($contentClass)
    {
        $this->contentClass = $contentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'FAQ List Page';
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
            $this->blocks = new ArrayCollection(array(
                new ContentBlock($this->contentClass, 'main', 'Introduction', array()),
            ));
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
        return 'AppFaqBundle:Faq:index';
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'app_faq.faq.index';
    }
}
