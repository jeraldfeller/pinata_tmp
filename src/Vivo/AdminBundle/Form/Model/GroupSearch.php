<?php

namespace Vivo\AdminBundle\Form\Model;

use Vivo\UtilBundle\Form\Model\SearchList;

class GroupSearch extends SearchList
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->equalColumns = array('user_group.id');

        $this->likeColumns = array(
            'user_group.name',
        );
    }
}
