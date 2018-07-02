<?php

namespace App\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vivo\AssetBundle\Model\Asset as BaseAsset;

/**
 * FruitImage.
 *
 * @ORM\Entity
 */
class FruitImage extends BaseAsset
{
    /**
     * @var Fruit
     *
     * @ORM\ManyToOne(targetEntity="Fruit")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $fruit;

    /**
     * @return Fruit
     */
    public function getFruit()
    {
        return $this->fruit;
    }

    /**
     * Set fruit.
     *
     * @param Fruit $fruit
     */
    public function setFruit(Fruit $fruit)
    {
        $this->fruit = $fruit;

        return $this;
    }
}
