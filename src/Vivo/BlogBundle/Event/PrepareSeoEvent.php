<?php

namespace Vivo\BlogBundle\Event;

use Knp\Component\Pager\Pagination\AbstractPagination;
use Vivo\BlogBundle\Model\CategoryInterface;
use Vivo\BlogBundle\Model\PostInterface;
use Vivo\PageBundle\Event\AbstractPageEvent;

class PrepareSeoEvent extends AbstractPageEvent
{
    /**
     * @var \Vivo\BlogBundle\Model\PostInterface
     */
    private $post;

    /**
     * @var \Vivo\BlogBundle\Model\CategoryInterface
     */
    private $category;

    /**
     * @var \DateTime
     */
    private $archiveDate;

    /**
     * @var AbstractPagination
     */
    private $pagination;

    /**
     * @param PostInterface $post
     *
     * @return $this
     */
    public function setPost(PostInterface $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return PostInterface
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param CategoryInterface $category
     *
     * @return $this
     */
    public function setCategory(CategoryInterface $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \DateTime $archiveDate
     *
     * @return $this
     */
    public function setArchiveDate(\DateTime $archiveDate = null)
    {
        $this->archiveDate = $archiveDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getArchiveDate()
    {
        return $this->archiveDate;
    }

    /**
     * @param AbstractPagination $pagination
     */
    public function setPagination(AbstractPagination $pagination)
    {
        $this->pagination = $pagination;

        return $this;
    }

    /**
     * @return AbstractPagination
     */
    public function getPagination()
    {
        return $this->pagination;
    }
}
