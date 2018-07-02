<?php

namespace Vivo\PageBundle\PageType\Block;

interface AssetGroupBlockInterface extends BlockInterface
{
    /**
     * @return \Vivo\PageBundle\Model\AssetGroupInterface
     */
    public function getModel();
}
