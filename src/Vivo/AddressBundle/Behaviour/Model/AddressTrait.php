<?php

namespace Vivo\AddressBundle\Behaviour\Model;

use Symfony\Component\Intl\Intl;
use Vivo\AddressBundle\Model\Locality;
use Vivo\AddressBundle\Model\Point;
use Vivo\AddressBundle\Model\PointInterface;

trait AddressTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $addressLine1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $addressLine2;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $postcode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    protected $suburb;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     */
    protected $state;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", nullable=true, scale=7)
     */
    protected $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", nullable=true, scale=7)
     */
    protected $longitude;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    protected $zoom;

    /**
     * @var \Vivo\AddressBundle\Model\LocalityInterface
     */
    protected $locality;

    /**
     * Get countryCode.
     *
     * @return string
     */
    abstract public function getCountryCode();

    /**
     * Get country name.
     *
     * @return string|null
     */
    public function getCountryName()
    {
        if (null !== $this->getCountryCode()) {
            return Intl::getRegionBundle()->getCountryName($this->getCountryCode());
        }

        return;
    }

    /**
     * Set addressLine1.
     *
     * @param string $addressLine1
     *
     * @return $this
     */
    public function setAddressLine1($addressLine1)
    {
        $this->addressLine1 = null === $addressLine1 ? null : (string) $addressLine1;

        return $this;
    }

    /**
     * Get addressLine1.
     *
     * @return string
     */
    public function getAddressLine1()
    {
        return $this->addressLine1;
    }

    /**
     * Set addressLine2.
     *
     * @param string $addressLine2
     *
     * @return $this
     */
    public function setAddressLine2($addressLine2)
    {
        $this->addressLine2 = null === $addressLine2 ? null : (string) $addressLine2;

        return $this;
    }

    /**
     * Get addressLine2.
     *
     * @return string
     */
    public function getAddressLine2()
    {
        return $this->addressLine2;
    }

    /**
     * Set postcode.
     *
     * @param int $postcode
     *
     * @return $this
     */
    public function setPostcode($postcode)
    {
        $this->postcode = null === $postcode ? null : (int) $postcode;

        return $this;
    }

    /**
     * Get postcode.
     *
     * @return int
     */
    public function getPostcode()
    {
        return $this->postcode;
    }

    /**
     * Set suburb.
     *
     * @param string $suburb
     *
     * @return $this
     */
    public function setSuburb($suburb)
    {
        $this->suburb = null === $suburb ? null : (string) $suburb;

        return $this;
    }

    /**
     * Get suburb.
     *
     * @return string
     */
    public function getSuburb()
    {
        return $this->suburb;
    }

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = null === $state ? null : (string) $state;

        return $this;
    }

    /**
     * Get state.
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set latLng.
     *
     * @param PointInterface $latLng
     *
     * @return $this
     */
    public function setLatLng(PointInterface $latLng)
    {
        $this->latitude = $latLng->getLatitude();
        $this->longitude = $latLng->getLongitude();
        $this->zoom = $latLng->getZoom();

        return $this;
    }

    /**
     * Get latLng.
     *
     * @return PointInterface
     */
    public function getLatLng()
    {
        return new Point($this->latitude, $this->longitude, $this->zoom);
    }

    /**
     * Get locality.
     *
     * @return \Vivo\AddressBundle\Model\LocalityInterface
     */
    public function getLocality()
    {
        if (null === $this->locality) {
            $this->locality = new Locality($this);
        }

        return $this->locality;
    }
}
