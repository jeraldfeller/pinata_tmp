<?php

namespace Vivo\BlogBundle\Form\Model;

use Vivo\UtilBundle\Form\Model\SearchList;
use Vivo\BlogBundle\Model\Category;

class CategorySearch extends SearchList
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->equalColumns = array('category.id');

        $this->likeColumns = array(
            'category.title',
        );
    }
}
