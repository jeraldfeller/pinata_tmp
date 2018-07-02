<?php

namespace Vivo\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vivo\AddressBundle\Model\PointInterface;

class PointValidator extends ConstraintValidator
{
    /**
     * @param mixed            $value
     * @param Constraint|Point $constraint
     *
     * @return bool
     *
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return true;
        }

        if (!$value instanceof PointInterface) {
            throw new UnexpectedTypeException($value, '\Vivo\\AddressBundle\\Model\\PointInterface');
        }

        $valid = true;

        if (null === $value->getLatitude() || null === $value->getLongitude()) {
            $valid = false;
        } elseif (($value->getLatitude() > 90 || $value->getLatitude() < -90)) {
            // Latitude has a max of 90 degrees and a min of -90 degrees
            $valid = false;
        } elseif (($value->getLongitude() > 180 || $value->getLongitude() < -180)) {
            // Latitude has a max of 90 degrees and a min of -90 degrees
            $valid = false;
        } elseif (!(0 != $value->getLatitude()) && !(0 != $value->getLongitude())) {
            $valid = false;
        }

        if (!$valid) {
            $this->context->addViolation($constraint->message);
        }

        return true;
    }
}
