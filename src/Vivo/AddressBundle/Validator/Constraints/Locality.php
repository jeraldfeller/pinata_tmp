<?php

namespace Vivo\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class Locality extends Constraint
{
    /**
     * @var string
     */
    public $invalidSuburbMessage = 'This value is not valid.';

    /**
     * @var string
     */
    public $blankStateMessage = 'This value should not be blank.';

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'vivo_address.validator.locality';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
