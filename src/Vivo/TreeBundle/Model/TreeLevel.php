<?php

namespace Vivo\TreeBundle\Model;

use Symfony\Component\PropertyAccess\PropertyAccess;

class TreeLevel implements TreeLevelInterface
{
    /**
     * @var TreeInterface
     */
    protected $model;

    /**
     * @var int
     */
    protected $level;

    /**
     * @var string
     */
    protected $labelProperty;

    /**
     * @param string $model
     * @param int    $level
     * @param string $label
     */
    public function __construct(TreeInterface $model, $level, $labelProperty = null)
    {
        $this->model = $model;
        $this->level = (int) $level;
        $this->labelProperty = (string) $labelProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function setLabelProperty($labelProperty)
    {
        $this->labelProperty = (string) $labelProperty;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabelProperty()
    {
        return $this->labelProperty;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        if (null === $this->model || !$this->labelProperty) {
            return '';
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        return $accessor->getValue($this->model, $this->labelProperty);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel();
    }
}
