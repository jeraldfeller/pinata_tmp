<?php

namespace App\TeamBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vivo\AssetBundle\Model\Asset as BaseAsset;

/**
 * ProfileImage.
 *
 * @ORM\Entity
 */
class ProfileImage extends BaseAsset
{
    /**
     * @var Profile
     *
     * @ORM\OneToOne(targetEntity="Profile")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $profile;

    /**
     * Set profile.
     *
     * @param Profile $profile
     *
     * @return $this
     */
    public function setProfile(Profile $profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile.
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Get models to cascade the updated timestamp to.
     *
     * @return \Vivo\UtilBundle\Behaviour\Model\TimestampableTrait[]
     */
    public function getCascadedTimestampableFields()
    {
        return array($this->profile);
    }
}
