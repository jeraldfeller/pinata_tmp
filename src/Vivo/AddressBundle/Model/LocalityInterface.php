<?php

namespace Vivo\AddressBundle\Model;

interface LocalityInterface
{
    /**
     * Set countryCode.
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode);

    /**
     * Get countryCode.
     *
     * @return string
     */
    public function getCountryCode();

    /**
     * Get country name.
     *
     * @return string|null
     */
    public function getCountryName();

    /**
     * Set postcode.
     *
     * @param int $postcode
     *
     * @return $this
     */
    public function setPostcode($postcode);

    /**
     * Get postcode.
     *
     * @return int
     */
    public function getPostcode();

    /**
     * Set suburb.
     *
     * @param string $suburb
     *
     * @return $this
     */
    public function setSuburb($suburb);

    /**
     * Get suburb.
     *
     * @return string
     */
    public function getSuburb();

    /**
     * Set state.
     *
     * @param string $state
     *
     * @return $this
     */
    public function setState($state);

    /**
     * Get state.
     *
     * @return string
     */
    public function getState();

    /**
     * Set latLng.
     *
     * @param PointInterface $latLng
     *
     * @return $this
     */
    public function setLatLng(PointInterface $latLng);

    /**
     * Get latLng.
     *
     * @return PointInterface
     */
    public function getLatLng();
}
