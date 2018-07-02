<?php

namespace Vivo\AddressBundle\Behaviour\Model;

trait AustralianAddressTrait
{
    use AddressTrait;

    /**
     * Set countryCode.
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        return $this;
    }

    /**
     * Get countryCode.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return 'AU';
    }
}
