<?php

namespace App\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vivo\AssetBundle\Model\Asset as BaseAsset;

/**
 * FarmImage.
 *
 * @ORM\Entity
 */
class FarmImage extends BaseAsset
{
    /**
     * @var Farm
     *
     * @ORM\ManyToOne(targetEntity="Farm")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $farm;

    /**
     * @return Farm
     */
    public function getFarm()
    {
        return $this->farm;
    }

    /**
     * Set farm.
     *
     * @param Farm $farm
     */
    public function setFarm(Farm $farm)
    {
        $this->farm = $farm;

        return $this;
    }
}
