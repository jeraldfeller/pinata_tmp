<?php

namespace Vivo\AdminBundle\Form\Model;

use Doctrine\ORM\QueryBuilder;
use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\AdminBundle\Model\GroupInterface;

class UserSearch extends SearchList implements UserSearchInterface
{
    /**
     * @var GroupInterface
     */
    protected $group;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->equalColumns = array('user.id');

        $this->likeColumns = array(
            'concat(concat(user.firstName, \' \'), user.lastName)',
            'user.firstName', 'user.lastName', 'user.email', 'user_group.name',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup(GroupInterface $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder = parent::getSearchQueryBuilder($queryBuilder);

        if (null !== $this->group) {
            $queryBuilder->andWhere('user.group = :group')
                ->setParameter('group', $this->group);
        }

        return $queryBuilder;
    }
}
