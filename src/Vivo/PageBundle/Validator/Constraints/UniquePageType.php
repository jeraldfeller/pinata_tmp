<?php

namespace Vivo\PageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniquePageType extends Constraint
{
    public $message = 'This page type is already in use.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'vivo_page.validator.unique_page_type';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
