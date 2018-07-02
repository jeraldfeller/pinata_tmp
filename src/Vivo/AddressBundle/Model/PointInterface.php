<?php

namespace Vivo\AddressBundle\Model;

interface PointInterface
{
    /**
     * Set latitude.
     *
     * @param float $latitude
     *
     * @return $this
     */
    public function setLatitude($latitude);

    /**
     * Get latitude.
     *
     * @return float
     */
    public function getLatitude();

    /**
     * Set longitude.
     *
     * @param float $longitude
     *
     * @return $this
     */
    public function setLongitude($longitude);

    /**
     * Get longitude.
     *
     * @return float
     */
    public function getLongitude();

    /**
     * Set zoom.
     *
     * @param int $zoom
     *
     * @return $this
     */
    public function setZoom($zoom);

    /**
     * Get zoom.
     *
     * @return int
     */
    public function getZoom();
}
