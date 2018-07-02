<?php

namespace Vivo\AddressBundle\Model;

class Point implements PointInterface
{
    /**
     * @var float
     */
    protected $latitude;

    /**
     * @var float
     */
    protected $longitude;

    /**
     * @var int
     */
    protected $zoom;

    /**
     * @param float $latitude
     * @param float $longitude
     * @param int   $zoom
     */
    public function __construct($latitude = null, $longitude = null, $zoom = null)
    {
        $this->latitude = null === $latitude ? null : (float) $latitude;
        $this->longitude = null === $longitude ? null : (float) $longitude;
        $this->zoom = null === $zoom ? null : (int) $zoom;
    }

    /**
     * {@inheritdoc}
     */
    public function setLatitude($latitude)
    {
        $this->latitude = null === $latitude ? null : (float) $latitude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * {@inheritdoc}
     */
    public function setLongitude($longitude)
    {
        $this->longitude = null === $longitude ? null : (float) $longitude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * {@inheritdoc}
     */
    public function setZoom($zoom)
    {
        $this->zoom = (int) $zoom;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getZoom()
    {
        return $this->zoom;
    }
}
