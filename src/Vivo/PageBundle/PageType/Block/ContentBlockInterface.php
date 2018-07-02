<?php

namespace Vivo\PageBundle\PageType\Block;

interface ContentBlockInterface extends BlockInterface
{
    /**
     * @return \Vivo\PageBundle\Model\ContentInterface
     */
    public function getModel();
}
