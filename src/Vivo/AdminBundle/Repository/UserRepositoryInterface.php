<?php

namespace Vivo\AdminBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\AdminBundle\Model\UserInterface;

interface UserRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserInterface $user
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getUsersUnderUserQueryBuilder(UserInterface $user);

    /**
     * @param UserInterface $user
     * @param int           $userId
     *
     * @return \Vivo\AdminBundle\Model\UserInterface|null
     */
    public function findOneUserUnderUser(UserInterface $user, $userId);

    /**
     * Find user by username.
     *
     * @param $username
     *
     * @return \Vivo\AdminBundle\Model\UserInterface
     */
    public function findOneByUsername($username);

    /**
     * @param array $criteria
     *
     * @return \Vivo\AdminBundle\Model\UserInterface|null
     */
    public function findNonDeletedUserBy(array $criteria);

    /**
     * @param array $criteria
     *
     * @return \Vivo\AdminBundle\Model\UserInterface[]
     */
    public function findNonDeletedUsersBy(array $criteria);

    /**
     * Creates a new instance of UserInterface.
     *
     * @return \Vivo\AdminBundle\Model\UserInterface
     */
    public function createUser();
}
