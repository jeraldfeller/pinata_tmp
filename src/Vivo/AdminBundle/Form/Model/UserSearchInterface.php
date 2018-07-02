<?php

namespace Vivo\AdminBundle\Form\Model;

use Vivo\AdminBundle\Model\GroupInterface;

interface UserSearchInterface
{
    /**
     * Set group.
     *
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function setGroup(GroupInterface $group = null);

    /**
     * Get group.
     *
     * @return GroupInterface
     */
    public function getGroup();
}
