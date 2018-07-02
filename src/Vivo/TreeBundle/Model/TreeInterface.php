<?php

namespace Vivo\TreeBundle\Model;

use Vivo\UtilBundle\Behaviour\Model\AutoFlushCacheInterface;

interface TreeInterface extends AutoFlushCacheInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId();

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank();

    /**
     * Get the parent object.
     *
     * @return TreeInterface
     */
    public function getParent();

    /**
     * @return TreeInterface[]
     */
    public function getChildren();
}
