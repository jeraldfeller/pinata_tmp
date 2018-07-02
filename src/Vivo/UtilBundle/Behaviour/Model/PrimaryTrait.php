<?php

namespace Vivo\UtilBundle\Behaviour\Model;

trait PrimaryTrait
{
    /**
     * @var bool
     */
    protected $primary;

    /**
     * Set primary.
     *
     * @param bool $primary
     *
     * @return $this
     */
    public function setPrimary($primary)
    {
        $this->primary = (bool) $primary;

        return $this;
    }

    /**
     * Get primary.
     *
     * @return bool
     */
    public function isPrimary()
    {
        return $this->primary;
    }
}
