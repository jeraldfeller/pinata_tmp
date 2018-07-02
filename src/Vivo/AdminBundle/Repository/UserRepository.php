<?php

namespace Vivo\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Vivo\AdminBundle\Model\GroupInterface;
use Vivo\AdminBundle\Model\UserInterface;

class UserRepository extends EntityRepository implements UserRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function getUsersUnderUserQueryBuilder(UserInterface $user)
    {
        return $this->createQueryBuilder('user')
            ->addSelect('user_group')
            ->leftJoin('user.group', 'user_group')
            ->where('(user_group.rank > :rank or user.group is NULL)')
            ->andWhere('user.deletedAt IS NULL')
            ->setParameter('rank', $user->getGroup()->getRank(true));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneUserUnderUser(UserInterface $user, $userId)
    {
        return $this->getUsersUnderUserQueryBuilder($user)
            ->andWhere('user.id = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersInGroupQueryBuilder(GroupInterface $group)
    {
        @trigger_error('getUsersInGroupQueryBuilder() is deprecated since version 1.6 and will be removed in 2.0. Use getUsersUnderUserQueryBuilder instead.', E_USER_DEPRECATED);

        return $this->createQueryBuilder('user')
            ->addSelect('user_group')
            ->leftJoin('user.group', 'user_group')
            ->where('(user_group.rank > :rank or user.group is NULL)')
            ->andWhere('user.deletedAt IS NULL')
            ->setParameter('rank', $group->getRank(true));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneUserInGroup(GroupInterface $group, $userId)
    {
        @trigger_error('findOneUserInGroup() is deprecated since version 1.6 and will be removed in 2.0. Use findOneUserUnderUser instead.', E_USER_DEPRECATED);

        return $this->getUsersInGroupQueryBuilder($group)
            ->andWhere('user.id = :user_id')
            ->setParameter('user_id', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUsername($username)
    {
        return $this->createQueryBuilder('user')
            ->addSelect('user_group')
            ->leftJoin('user.group', 'user_group')
            ->where('user.email = :email')
            ->setParameter('email', $username)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findNonDeletedUserBy(array $criteria)
    {
        return $this->findOneBy(array_merge($criteria, array(
            'deletedAt' => null,
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function findNonDeletedUsersBy(array $criteria)
    {
        return $this->findBy(array_merge($criteria, array(
            'deletedAt' => null,
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function createUser()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }
}
