<?php

namespace App\SiteBundle\Entity;

use Vivo\SiteBundle\Model\Site as BaseSite;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vivo_site")
 */
class Site extends BaseSite
{
    /**
     * @var string
     *
     * @ORM\Column(name="contact_email", type="string", length=255, nullable=true)
     */
    protected $contactEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=255, nullable=true)
     */
    protected $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="post_office_box", type="string", length=255, nullable=true)
     */
    protected $postOfficeBox;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=255, nullable=true)
     */
    protected $address;

    /**
     * @var string
     *
     * @ORM\Column(name="facebookURL", type="string", length=255, nullable=true)
     */
    protected $facebookURL;

    /**
     * @var string
     *
     * @ORM\Column(name="youtubeURL", type="string", length=255, nullable=true)
     */
    protected $youtubeURL;

    /**
     * @var string
     *
     * @ORM\Column(name="instagramURL", type="string", length=255, nullable=true)
     */
    protected $instagramURL;

    /**
     * @var SiteLogo
     *
     * @ORM\OneToOne(targetEntity="Vivo\AssetBundle\Model\AssetInterface", orphanRemoval=true, cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="site_logo", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $siteLogo;

    /**
     * @var string
     *
     * @ORM\Column(name="berry_world_email", type="string", length=255, nullable=true)
     */
    protected $berryWorldEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="berry_world_website", type="string", length=255, nullable=true)
     */
    protected $berryWorldWebsite;

    /**
     * @return \App\SiteBundle\Entity\SiteLogo
     */
    public function getSiteLogo()
    {
        return $this->siteLogo;
    }

    /**
     * Set siteLogo.
     *
     * @param \App\SiteBundle\Entity\SiteLogo $siteLogo
     */
    public function setSiteLogo(\App\SiteBundle\Entity\SiteLogo $siteLogo = null)
    {
        $this->siteLogo = $siteLogo;

        return $this;
    }

    /**
     * Set address.
     *
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set contactEmail.
     *
     * @param string $contactEmail
     */
    public function setContactEmail($contactEmail)
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contactEmail;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set facebookURL.
     *
     * @param string $facebookURL
     */
    public function setFacebookURL($facebookURL)
    {
        $this->facebookURL = $facebookURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getFacebookURL()
    {
        return $this->facebookURL;
    }

    /**
     * Set youtubeURL.
     *
     * @param string $youtubeURL
     */
    public function setYoutubeURL($youtubeURL)
    {
        $this->youtubeURL = $youtubeURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getYoutubeURL()
    {
        return $this->youtubeURL;
    }

    /**
     * Set instagramURL.
     *
     * @param string $instagramURL
     */
    public function setInstagramURL($instagramURL)
    {
        $this->instagramURL = $instagramURL;

        return $this;
    }

    /**
     * @return string
     */
    public function getInstagramURL()
    {
        return $this->instagramURL;
    }

    /**
     * Set fax.
     *
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set postOfficeBox.
     *
     * @param string $postOfficeBox
     */
    public function setPostOfficeBox($postOfficeBox)
    {
        $this->postOfficeBox = $postOfficeBox;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostOfficeBox()
    {
        return $this->postOfficeBox;
    }

    /**
     * Set berryWorldEmail.
     *
     * @param string $berryWorldEmail
     */
    public function setberryWorldEmail($berryWorldEmail)
    {
        $this->berryWorldEmail = $berryWorldEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getberryWorldEmail()
    {
        return $this->berryWorldEmail;
    }

    /**
     * Set berryWorldWebsite.
     *
     * @param string $berryWorldWebsite
     */
    public function setBerryWorldWebsite($berryWorldWebsite)
    {
        $this->berryWorldWebsite = $berryWorldWebsite;

        return $this;
    }

    /**
     * @return string
     */
    public function getBerryWorldWebsite()
    {
        return $this->berryWorldWebsite;
    }
}
