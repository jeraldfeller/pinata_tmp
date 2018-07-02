<?php

namespace Vivo\UtilBundle\Validator;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PasswordStrengthValidator extends ConstraintValidator
{
    /**
     * @param string           $value
     * @param PasswordStrength $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value) {
            return;
        }

        if (!is_scalar($value) && !(is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        if (strlen($value) < $constraint->minLength) {
            $this->context->addViolation($constraint->minLengthMessage, array(
                '{{ fieldName }}' => $constraint->fieldName,
                '{{ min }}' => $constraint->minLength,
                '{{ plural }}' => 1 === $constraint->minLengthMessage ? 'character' : 'characters',
            ));
        }

        if (strlen(preg_replace('/[^a-z]/', null, $value)) < $constraint->minLowercase) {
            $this->context->addViolation($constraint->minLowercaseMessage, array(
                '{{ fieldName }}' => $constraint->fieldName,
                '{{ min }}' => $constraint->minLowercase,
                '{{ plural }}' => 1 === $constraint->minLowercase ? 'character' : 'characters',
            ));
        }

        if (strlen(preg_replace('/[^A-Z]/', null, $value)) < $constraint->minUppercase) {
            $this->context->addViolation($constraint->minUppercaseMessage, array(
                '{{ fieldName }}' => $constraint->fieldName,
                '{{ min }}' => $constraint->minUppercase,
                '{{ plural }}' => 1 === $constraint->minUppercase ? 'character' : 'characters',
            ));
        }

        if (strlen(preg_replace('/[0-9]/', null, $value)) < $constraint->minLetterLength) {
            $this->context->addViolation($constraint->minLetterLengthMessage, array(
                '{{ fieldName }}' => $constraint->fieldName,
                '{{ min }}' => $constraint->minLetterLength,
                '{{ plural }}' => 1 === $constraint->minLetterLength ? 'letter' : 'letters',
            ));
        }

        if (strlen(preg_replace('/[^0-9]/', null, $value)) < $constraint->minNumberLength) {
            $this->context->addViolation($constraint->minNumberLengthMessage, array(
                '{{ fieldName }}' => $constraint->fieldName,
                '{{ min }}' => $constraint->minNumberLength,
                '{{ plural }}' => 1 === $constraint->minNumberLength ? 'number' : 'numbers',
            ));
        }
    }
}
