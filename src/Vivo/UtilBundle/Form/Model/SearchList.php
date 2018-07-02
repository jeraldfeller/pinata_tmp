<?php

namespace Vivo\UtilBundle\Form\Model;

use Doctrine\ORM\QueryBuilder;

class SearchList
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var array
     */
    protected $likeColumns;

    /**
     * @var array
     */
    protected $equalColumns;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->likeColumns = [];
        $this->equalColumns = [];
    }

    /**
     * Set query.
     *
     * @param string $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = (string) $query;

        return $this;
    }

    /**
     * Get query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set like columns.
     *
     * @param array $columns
     *
     * @return $this
     */
    public function setLikeColumns(array $columns)
    {
        $this->likeColumns = $columns;

        return $this;
    }

    /**
     * Get like columns.
     *
     * @return array
     */
    public function getLikeColumns()
    {
        return $this->likeColumns;
    }

    /**
     * Set equal columns.
     *
     * @param array $columns
     *
     * @return $this
     */
    public function setEqualColumns(array $columns)
    {
        $this->equalColumns = $columns;

        return $this;
    }

    /**
     * Get equal columns.
     *
     * @return array
     */
    public function getEqualColumns()
    {
        return $this->equalColumns;
    }

    /**
     * Get search query builder.
     *
     * @param QueryBuilder $queryBuilder
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(QueryBuilder $queryBuilder)
    {
        if ($this->query) {
            $orWhere = $queryBuilder->expr()->orX();

            $i = 0;
            foreach ($this->getLikeColumns() as $col) {
                $colParamName = 'filter_like_'.(++$i);

                $orWhere->add($queryBuilder->expr()->like($col, ':'.$colParamName));
                $queryBuilder->setParameter($colParamName, '%'.$this->getQuery().'%');
            }

            $i = 0;
            foreach ($this->getEqualColumns() as $col) {
                ++$i;
                $colParamName = 'filter_equal_'.($i++);

                $orWhere->add($queryBuilder->expr()->in($col, ':'.$colParamName));
                $queryBuilder->setParameter($colParamName, $this->getQuery());
            }

            $queryBuilder->andWhere($orWhere);
        }

        return $queryBuilder;
    }
}
