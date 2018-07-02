<?php

namespace Vivo\PageBundle\Routing;

use Vivo\PageBundle\Model\PageInterface;

class PageReference implements \Serializable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $pageTypeAlias;

    public function __construct(PageInterface $page = null)
    {
        if (null !== $page) {
            $this->id = $page->getId();

            if ($page->getPageTypeInstance()) {
                $this->pageTypeAlias = $page->getPageTypeInstance()->getAlias();
            }
        }
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPageTypeAlias()
    {
        return $this->pageTypeAlias;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array(
            'id' => $this->getId(),
            'pageTypeAlias' => $this->getPageTypeAlias(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        $this->id = $data['id'];
        $this->pageTypeAlias = $data['pageTypeAlias'];
    }
}
