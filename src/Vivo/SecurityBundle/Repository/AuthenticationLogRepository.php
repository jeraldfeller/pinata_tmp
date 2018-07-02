<?php

namespace Vivo\SecurityBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Vivo\SecurityBundle\Model\AuthenticationLogInterface;

class AuthenticationLogRepository extends EntityRepository implements AuthenticationLogRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function purge()
    {
        return $this->createQueryBuilder('authentication_log')
            ->andWhere('authentication_log.loggedAt < :delete_before')
            ->setParameter('delete_before', new \DateTime('-6 months'))
            ->delete()
            ->getQuery()
            ->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function countIpFailuresSince($firewallName, $ipAddress, \DateTime $from)
    {
        try {
            return $this->createQueryBuilder('authentication_log')
                ->select('count(authentication_log.id)')
                ->andWhere('authentication_log.firewallName = :firewall_name')
                ->setParameter('firewall_name', $firewallName)
                ->andWhere('authentication_log.ipAddress = :ip_address')
                ->setParameter('ip_address', $ipAddress)
                ->andWhere('authentication_log.status = :status_failure')
                ->setParameter('status_failure', AuthenticationLogInterface::STATUS_FAILURE)
                ->andWhere('authentication_log.loggedAt >= :from')
                ->setParameter('from', $from)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countIpAndUsernameFailuresSince($firewallName, $ipAddress, $username, \DateTime $from)
    {
        try {
            return $this->createQueryBuilder('authentication_log')
                ->select('count(authentication_log.id)')
                ->andWhere('authentication_log.firewallName = :firewall_name')
                ->setParameter('firewall_name', $firewallName)
                ->andWhere('authentication_log.ipAddress = :ip_address')
                ->setParameter('ip_address', $ipAddress)
                ->andWhere('authentication_log.username = :username')
                ->setParameter('username', $username)
                ->andWhere('authentication_log.status = :status_failure')
                ->setParameter('status_failure', AuthenticationLogInterface::STATUS_FAILURE)
                ->andWhere('authentication_log.loggedAt >= :from')
                ->setParameter('from', $from)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function countUsernameFailuresSince($firewallName, $username, \DateTime $from)
    {
        try {
            return $this->createQueryBuilder('authentication_log')
                ->select('count(authentication_log.id)')
                ->andWhere('authentication_log.firewallName = :firewall_name')
                ->setParameter('firewall_name', $firewallName)
                ->andWhere('authentication_log.username = :username')
                ->setParameter('username', $username)
                ->andWhere('authentication_log.status = :status_failure')
                ->setParameter('status_failure', AuthenticationLogInterface::STATUS_FAILURE)
                ->andWhere('authentication_log.loggedAt >= :from')
                ->setParameter('from', $from)
                ->getQuery()
                ->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLastSucessfulAttemptAt($firewallName, $ipAddress, $username)
    {
        try {
            $date = $this->createQueryBuilder('authentication_log')
                ->select('authentication_log.loggedAt')
                ->andWhere('authentication_log.firewallName = :firewall_name')
                ->setParameter('firewall_name', $firewallName)
                ->andWhere('authentication_log.ipAddress = :ip_address')
                ->setParameter('ip_address', $ipAddress)
                ->andWhere('authentication_log.username = :username')
                ->setParameter('username', $username)
                ->andWhere('authentication_log.status = :status_success')
                ->setParameter('status_success', AuthenticationLogInterface::STATUS_SUCCESS)
                ->addOrderBy('authentication_log.loggedAt', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();

            return new \DateTime($date);
        } catch (NoResultException $e) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createAuthenticationLog()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }
}
