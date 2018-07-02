<?php

namespace Vivo\AdminBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Vivo\AdminBundle\Model\GroupInterface;
use Vivo\AdminBundle\Model\UserInterface;

interface GroupRepositoryInterface extends ObjectRepository
{
    /**
     * @param UserInterface $user
     * @param bool          $ignoreSelf
     *
     * @return \Vivo\AdminBundle\Model\GroupInterface[]
     */
    public function findGroupsUnderUser(UserInterface $user, $ignoreSelf);

    /**
     * @param UserInterface $user
     * @param bool          $ignoreSelf
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getGroupsUnderUserQueryBuilder(UserInterface $user, $ignoreSelf);

    /**
     * @param int           $groupId
     * @param UserInterface $user
     * @param bool          $ignoreSelf
     *
     * @return GroupInterface
     */
    public function findOneGroupByIdUnderUser($groupId, UserInterface $user, $ignoreSelf = false);

    /**
     * Count posts for categories.
     *
     * @param \Vivo\AdminBundle\Model\GroupInterface[] $groups
     */
    public function countUsersForGroups(array $groups);

    /**
     * Creates a new instance of GroupInterface.
     *
     * @return \Vivo\AdminBundle\Model\GroupInterface
     */
    public function createGroup();
}
