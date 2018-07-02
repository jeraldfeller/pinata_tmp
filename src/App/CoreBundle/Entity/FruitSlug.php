<?php

namespace App\CoreBundle\Entity;

use Vivo\SlugBundle\Model\Slug as BaseSlug;
use Doctrine\ORM\Mapping as ORM;

/**
 * Fruit Slug.
 *
 * @ORM\Entity
 */
class FruitSlug extends BaseSlug
{
    /**
     * @ORM\ManyToOne(targetEntity="Fruit", inversedBy="slugs")
     * @ORM\JoinColumn(name="fruit_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $fruit;

    /**
     * Set fruit.
     *
     * @param mixed $fruit
     */
    public function setFruit($fruit)
    {
        $this->fruit = $fruit;

        return $this;
    }

    /**
     * Get fruit.
     *
     * @return mixed
     */
    public function getFruit()
    {
        return $this->fruit;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return $this === $this->fruit->getPrimarySlug() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return;
    }
}
