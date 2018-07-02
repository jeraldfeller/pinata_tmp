<?php

namespace Vivo\PageBundle\PageType\Block;

class ContentBlock implements ContentBlockInterface
{
    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var \Vivo\PageBundle\Model\ContentInterface
     */
    protected $model;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var string|\Symfony\Component\Form\FormTypeInterface
     */
    protected $formType;

    /**
     * Constructor.
     *
     * @param string                                           $modelClass
     * @param string                                           $alias
     * @param string                                           $name
     * @param array                                            $options
     * @param string|\Symfony\Component\Form\FormTypeInterface $formType
     */
    public function __construct($modelClass, $alias, $name, array $options = array(), $formType = 'Trsteel\CkeditorBundle\Form\Type\CkeditorType')
    {
        $this->modelClass = $modelClass;
        $this->alias = $alias;
        $this->name = $name;
        $this->options = $options;
        $this->formType = $formType;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function getModel()
    {
        if (null === $this->model) {
            $modelReflection = new \ReflectionClass($this->modelClass);
            $this->model = $modelReflection->newInstance();
            $this->model->setAlias($this->alias);
        }

        return $this->model;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType()
    {
        return $this->formType;
    }
}
