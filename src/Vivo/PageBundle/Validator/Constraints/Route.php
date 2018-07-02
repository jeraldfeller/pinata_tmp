<?php

namespace Vivo\PageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Route extends Constraint
{
    /**
     * @var string
     */
    public $message = '\'{{ value }}\' is not available.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'vivo_page.validator.route';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
