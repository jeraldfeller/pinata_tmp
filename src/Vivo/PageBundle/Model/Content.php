<?php

namespace Vivo\PageBundle\Model;

use Vivo\UtilBundle\Behaviour\Model\TimestampableTrait;

/**
 * Content.
 */
class Content implements ContentInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var PageInterface
     */
    protected $page;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($alias)
    {
        $this->alias = null === $alias ? null : (string) $alias;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        $this->content = null === $content ? null : (string) $content;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(PageInterface $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get models to cascade the updated timestamp to.
     *
     * @return \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait[]
     */
    public function getCascadedTimestampableFields()
    {
        return array($this->getPage());
    }
}
