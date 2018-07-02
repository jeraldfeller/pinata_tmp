<?php

namespace Vivo\PageBundle\Model;

interface ContentInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set alias.
     *
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias);

    /**
     * Get alias.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return $this
     */
    public function setContent($content);

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent();

    /**
     * Set page.
     *
     * @param \Vivo\PageBundle\Model\PageInterface $page
     *
     * @return $this
     */
    public function setPage(PageInterface $page = null);

    /**
     * Get page.
     *
     * @return \Vivo\PageBundle\Model\PageInterface
     */
    public function getPage();

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt();
}
