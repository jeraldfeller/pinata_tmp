<?php

namespace Vivo\AddressBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;

/**
 * SuburbRepositoryInterface.
 */
interface SuburbRepositoryInterface extends ObjectRepository
{
    /**
     * Find all the suburbs for a postcode.
     *
     * @param string $countryCode
     * @param int    $postcode
     *
     * @return \Vivo\AddressBundle\Model\Suburb[]
     */
    public function findAllByPostcode($countryCode, $postcode);

    /**
     * Find one suburb for a postcode.
     *
     * @param string $countryCode
     * @param int    $postcode
     *
     * @return \Vivo\AddressBundle\Model\Suburb
     */
    public function findOneByPostcode($countryCode, $postcode);

    /**
     * Find one suburb by postcode and suburb.
     *
     * @param string $countryCode
     * @param int    $postcode
     * @param string $suburbName
     *
     * @return \Vivo\AddressBundle\Model\Suburb|null
     */
    public function findOneByPostcodeAndSuburb($countryCode, $postcode, $suburbName);
}
