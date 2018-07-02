<?php

namespace Vivo\AddressBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * SuburbRepository.
 */
class SuburbRepository extends EntityRepository implements SuburbRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findAllByPostcode($countryCode, $postcode)
    {
        $postcode = (int) $postcode;

        if (!$postcode) {
            return array();
        }

        return $this->createQueryBuilder('suburb')
            ->where('suburb.postcode = :postcode')
            ->setParameter('postcode', $postcode)
            ->orderBy('suburb.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPostcode($countryCode, $postcode)
    {
        $postcode = (int) $postcode;

        if (!$postcode) {
            return array();
        }

        return $this->createQueryBuilder('suburb')
            ->where('suburb.postcode = :postcode')
            ->setParameter('postcode', $postcode)
            ->orderBy('suburb.name', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByPostcodeAndSuburb($countryCode, $postcode, $suburbName)
    {
        $postcode = (int) $postcode;

        if (!$postcode) {
            return array();
        }

        return $this->createQueryBuilder('suburb')
            ->where('suburb.postcode = :postcode and suburb.name = :suburb')
            ->setParameter('postcode', $postcode)
            ->setParameter('suburb', $suburbName)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
