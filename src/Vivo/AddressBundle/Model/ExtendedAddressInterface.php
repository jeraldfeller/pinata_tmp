<?php

namespace Vivo\AddressBundle\Model;

/**
 * ExtendedAddressInterface.
 */
interface ExtendedAddressInterface extends AddressInterface
{
    /**
     * Set company.
     *
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company);

    /**
     * Get company.
     *
     * @return string
     */
    public function getCompany();

    /**
     * @return bool
     */
    public function isIgnored();

    /**
     * This is used to check if an address already exists.
     */
    public function getSignature();
}
