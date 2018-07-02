<?php

namespace Vivo\AddressBundle\Behaviour\Model;

trait GlobalAddressTrait
{
    use AddressTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code", type="string", length=2, nullable=false)
     */
    protected $countryCode;

    /**
     * Set countryCode.
     *
     * @param string $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        $this->countryCode = null === $countryCode ? null : (string) $countryCode;

        return $this;
    }

    /**
     * Get countryCode.
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->countryCode;
    }
}
