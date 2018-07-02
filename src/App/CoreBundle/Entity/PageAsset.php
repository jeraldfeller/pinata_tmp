<?php

namespace App\CoreBundle\Entity;

use Vivo\PageBundle\Model\Asset;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class PageAsset extends Asset
{
    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=20)
     */
    protected $colorClass;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $subtitle;
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=11, nullable=false, options={"default" : 1})
     */
    protected $status;
    
    /**
     * Set color.
     *
     * @param string $color
     */
    public function setColorClass($colorClass)
    {
        $this->colorClass = $colorClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->colorClass;
    }

    /**
     * Set subtitle.
     *
     * @param string $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }
    
    /**
     * Set status.
     *
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
}
