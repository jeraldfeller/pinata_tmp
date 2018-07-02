<?php

namespace Vivo\BlogBundle\Form\Model;

use Doctrine\ORM\QueryBuilder;
use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\BlogBundle\Model\Category;

class PostSearch extends SearchList implements PostSearchInterface
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @param bool $includeAuthorField
     */
    public function __construct($includeAuthorField)
    {
        parent::__construct();

        $this->equalColumns = array('post.id');

        $this->likeColumns = array(
            'post.title', 'post.excerpt', 'post.body',
        );

        if ((bool) $includeAuthorField) {
            $this->likeColumns[] = 'post.author';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder = parent::getSearchQueryBuilder($queryBuilder);

        if (null !== $this->category) {
            $queryBuilder->andWhere(':category MEMBER OF post.categories')
                ->setParameter('category', $this->category);
        }

        return $queryBuilder;
    }
}
