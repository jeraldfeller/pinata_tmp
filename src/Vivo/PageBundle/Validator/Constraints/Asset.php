<?php

namespace Vivo\PageBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Asset extends Constraint
{
    /**
     * @var string
     */
    public $message = '\'{{ filename }}\' is not a valid format.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'vivo_page.validator.asset';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
