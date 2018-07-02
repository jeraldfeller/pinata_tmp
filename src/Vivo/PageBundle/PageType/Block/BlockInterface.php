<?php

namespace Vivo\PageBundle\PageType\Block;

interface BlockInterface
{
    /**
     * Return the name of this block.
     *
     * @return string
     */
    public function getName();

    /**
     * Return the alias of this block.
     *
     * @return string
     */
    public function getAlias();

    /**
     * Get form type.
     *
     * @return string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getFormType();

    /**
     * Return the array options for the content field.
     *
     * @return array
     */
    public function getOptions();

    /**
     * @return object
     */
    public function getModel();
}
