<?php

namespace Vivo\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Vivo\AdminBundle\Model\GroupInterface;
use Vivo\AdminBundle\Model\UserInterface;

class GroupRepository extends EntityRepository implements GroupRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function findGroupsUnderUser(UserInterface $user, $ignoreSelf)
    {
        return $this->getGroupsUnderUserQueryBuilder($user, $ignoreSelf)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupsUnderUserQueryBuilder(UserInterface $user, $ignoreSelf)
    {
        $qb = $this->createQueryBuilder('user_group')
            ->where('user_group.rank > :rank')
            ->setParameter('rank', $user->getGroup()->getRank(true))
            ->orderBy('user_group.rank', 'asc');

        if ($ignoreSelf) {
            $qb->andWhere('user_group.id != :ignore_group_id')
                ->setParameter('ignore_group_id', $user->getGroup()->getId());
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findGroupsUnderGroup(GroupInterface $group, $ignoreSelf)
    {
        @trigger_error('findGroupsUnderGroup() is deprecated since version 1.6 and will be removed in 2.0. Use findGroupsUnderUser instead.', E_USER_DEPRECATED);

        return $this->getGroupsUnderGroupQueryBuilder($group, $ignoreSelf)
            ->getQuery()
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupsUnderGroupQueryBuilder(GroupInterface $group, $ignoreSelf)
    {
        @trigger_error('getGroupsUnderGroupQueryBuilder() is deprecated since version 1.6 and will be removed in 2.0. Use getGroupsUnderUserQueryBuilder instead.', E_USER_DEPRECATED);

        $qb = $this->createQueryBuilder('user_group')
            ->where('user_group.rank > :rank')
            ->setParameter('rank', $group->getRank(true))
            ->orderBy('user_group.rank', 'asc');

        if ($ignoreSelf) {
            $qb->andWhere('user_group.id != :ignore_group_id')
                ->setParameter('ignore_group_id', $group->getId());
        }

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneGroupByIdUnderUser($groupId, UserInterface $user, $ignoreSelf = false)
    {
        return $this->getGroupsUnderUserQueryBuilder($user, $ignoreSelf)
            ->andWhere('user_group.id = :group_id')
            ->setParameter('group_id', $groupId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function findOneGroupByIdUnderGroup($groupId, GroupInterface $group, $ignoreSelf = false)
    {
        @trigger_error('findGroupsUnderGroup() is deprecated since version 1.6 and will be removed in 2.0. Use findGroupsUnderUser instead.', E_USER_DEPRECATED);

        return $this->getGroupsUnderGroupQueryBuilder($group, $ignoreSelf)
            ->andWhere('user_group.id = :group_id')
            ->setParameter('group_id', $groupId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function countUsersForGroups(array $groups)
    {
        $results = $this->createQueryBuilder('user_group')
            ->select('user_group.id, count(user) as _count')
            ->innerJoin('user_group.users', 'user')
            ->andWhere('user_group in (:groups)')
            ->setParameter('groups', $groups)
            ->groupBy('user_group.id')
            ->getQuery()
            ->getArrayResult();

        $mapped = array();
        foreach ($results as $result) {
            $mapped[$result['id']] = (int) $result['_count'];
        }

        foreach ($groups as $group) {
            if (!array_key_exists($group->getId(), $mapped)) {
                $mapped[$group->getId()] = 0;
            }
        }

        return $mapped;
    }

    /**
     * {@inheritdoc}
     */
    public function createGroup()
    {
        return $this->getClassMetadata()->getReflectionClass()->newInstance();
    }
}
