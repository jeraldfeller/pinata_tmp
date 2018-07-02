<?php

namespace Vivo\PageBundle\Menu\Event;

use Symfony\Component\EventDispatcher\Event;
use Vivo\PageBundle\Menu\ItemInterface;

class ItemEvent extends Event
{
    /**
     * @var ItemInterface
     */
    protected $item;

    /**
     * @param ItemInterface $item
     */
    public function __construct(ItemInterface $item)
    {
        $this->item = $item;
    }

    /**
     * Get item.
     *
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }
}
