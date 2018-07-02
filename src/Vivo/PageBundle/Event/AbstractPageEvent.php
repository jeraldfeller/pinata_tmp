<?php

namespace Vivo\PageBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Vivo\PageBundle\Model\PageInterface;

abstract class AbstractPageEvent extends Event
{
    /**
     * @var \Vivo\PageBundle\Model\PageInterface
     */
    private $page;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param PageInterface $page
     * @param Request       $request
     */
    public function __construct(PageInterface $page, Request $request)
    {
        $this->page = $page;
        $this->request = $request;
    }

    /**
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
