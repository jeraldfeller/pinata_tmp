<?php

namespace App\TeamBundle\Entity;

use Vivo\SlugBundle\Model\Slug as BaseSlug;
use Doctrine\ORM\Mapping as ORM;

/**
 * Profile Slug.
 *
 * @ORM\Entity
 */
class ProfileSlug extends BaseSlug
{
    /**
     * @ORM\ManyToOne(targetEntity="Profile", inversedBy="slugs")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Set profile.
     *
     * @param mixed $profile
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile.
     *
     * @return mixed
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * {@inheritdoc}
     */
    public function isPrimary()
    {
        return $this === $this->profile->getPrimarySlug() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return;
    }
}
