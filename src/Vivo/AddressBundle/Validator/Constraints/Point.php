<?php

namespace Vivo\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Point extends Constraint
{
    /**
     * @var string
     */
    public $message = 'The latitude/longitude provided is not valid.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'vivo_address.validator.point';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
