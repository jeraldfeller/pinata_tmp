<?php

namespace Vivo\AddressBundle\Model;

class Locality implements LocalityInterface
{
    /**
     * @var LocalityInterface
     */
    protected $parentLocality;

    /**
     * Constructor.
     *
     * @param LocalityInterface $parentLocality
     */
    public function __construct(LocalityInterface $parentLocality)
    {
        $this->parentLocality = $parentLocality;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountryCode($countryCode)
    {
        $this->parentLocality->setCountryCode($countryCode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryCode()
    {
        return $this->parentLocality->getCountryCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryName()
    {
        return $this->parentLocality->getCountryName();
    }

    /**
     * {@inheritdoc}
     */
    public function setPostcode($postcode)
    {
        $this->parentLocality->setPostcode($postcode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPostcode()
    {
        return $this->parentLocality->getPostcode();
    }

    /**
     * {@inheritdoc}
     */
    public function setSuburb($suburb)
    {
        $this->parentLocality->setSuburb($suburb);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuburb()
    {
        return $this->parentLocality->getSuburb();
    }

    /**
     * {@inheritdoc}
     */
    public function setState($state)
    {
        $this->parentLocality->setState($state);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->parentLocality->getState();
    }

    /**
     * {@inheritdoc}
     */
    public function setLatLng(PointInterface $latLng)
    {
        $this->parentLocality->setLatLng($latLng);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatLng()
    {
        return $this->parentLocality->getLatLng();
    }
}
