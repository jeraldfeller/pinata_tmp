<?php

namespace Vivo\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class AuPostcode extends Constraint
{
    /**
     * @var string
     */
    public $message = 'This is not a valid Australian postcode.';
    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'vivo_address.validator.au_postcode';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
