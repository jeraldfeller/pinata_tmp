<?php

namespace Vivo\AddressBundle\Model;

/**
 * AddressInterface.
 */
interface AddressInterface extends LocalityInterface
{
    /**
     * Set addressLine1.
     *
     * @param string $addressLine1
     *
     * @return $this
     */
    public function setAddressLine1($addressLine1);

    /**
     * Get addressLine1.
     *
     * @return string
     */
    public function getAddressLine1();

    /**
     * Set addressLine2.
     *
     * @param string $addressLine2
     *
     * @return $this
     */
    public function setAddressLine2($addressLine2);

    /**
     * Get addressLine2.
     *
     * @return string
     */
    public function getAddressLine2();

    /**
     * Get locality.
     *
     * @return LocalityInterface
     */
    public function getLocality();
}
