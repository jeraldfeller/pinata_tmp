<?php

namespace Vivo\TreeBundle\Model;

interface TreeLevelInterface
{
    /**
     * Get model.
     *
     * @return TreeInterface
     */
    public function getModel();

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel();

    /**
     * Set labelProperty.
     *
     * @param string $labelProperty
     *
     * @return $this
     */
    public function setLabelProperty($labelProperty);

    /**
     * Get labelProperty.
     *
     * @return string
     */
    public function getLabelProperty();

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel();
}
