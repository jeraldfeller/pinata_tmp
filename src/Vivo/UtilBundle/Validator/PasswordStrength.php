<?php

namespace Vivo\UtilBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordStrength extends Constraint
{
    public $fieldName = 'Password';

    public $minLengthMessage = '{{ fieldName }} must be {{ min }} {{ plural }} long.';
    public $minLowercaseMessage = '{{ fieldName }} must contain {{ min }} lowercase {{ plural }}.';
    public $minUppercaseMessage = '{{ fieldName }} must contain {{ min }} uppercase {{ plural }}.';
    public $minLetterLengthMessage = '{{ fieldName }} must contain {{ min }} {{ plural }}.';
    public $minNumberLengthMessage = '{{ fieldName }} must contain {{ min }} {{ plural }}.';

    public $minLength;
    public $minLowercase;
    public $minUppercase;
    public $minLetterLength;
    public $minNumberLength;
}
