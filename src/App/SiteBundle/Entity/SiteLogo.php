<?php

namespace App\SiteBundle\Entity;

use Vivo\AssetBundle\Model\Asset as BaseAsset;
use Doctrine\ORM\Mapping as ORM;

/**
 * SiteLogo.
 *
 * @ORM\Entity
 */
class SiteLogo extends BaseAsset
{
    /**
     * @var Site
     *
     * @ORM\OneToOne(targetEntity="Vivo\SiteBundle\Model\SiteInterface")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $site;
}
