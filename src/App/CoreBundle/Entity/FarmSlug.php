<?php

namespace App\CoreBundle\Entity;

use Vivo\SlugBundle\Model\Slug as BaseSlug;
use Doctrine\ORM\Mapping as ORM;

/**
 * Farm Slug.
 *
 * @ORM\Entity
 */
class FarmSlug extends BaseSlug
{
    /**
     * @ORM\ManyToOne(targetEntity="Farm", inversedBy="slugs")
     * @ORM\JoinColumn(name="farm_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $farm;

    /**
     * Set farm.
     *
     * @param mixed $farm
     */
    public function setFarm($farm)
    {
        $this->farm = $farm;

        return $this;
    }

    /**
     * Get farm.
     *
     * @return mixed
     */
    public function getFarm()
    {
        return $this->farm;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return $this === $this->farm->getPrimarySlug() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return;
    }
}
