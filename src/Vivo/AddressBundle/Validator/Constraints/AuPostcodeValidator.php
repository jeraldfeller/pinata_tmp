<?php

namespace Vivo\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vivo\AddressBundle\Repository\SuburbRepositoryInterface;

class AuPostcodeValidator extends ConstraintValidator
{
    /**
     * @var \Vivo\AddressBundle\Repository\SuburbRepositoryInterface
     */
    protected $suburbRepository;

    /**
     * @param SuburbRepositoryInterface $suburbRepository
     */
    public function __construct(SuburbRepositoryInterface $suburbRepository)
    {
        $this->suburbRepository = $suburbRepository;
    }

    /**
     * @param string                $value
     * @param Constraint|AuPostcode $constraint
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

        $isValid = true;

        if (strlen($value) < 3 || strlen($value) > 4 || !ctype_digit((string) $value)) {
            $isValid = false;
        } elseif (!$this->suburbRepository->findOneByPostcode('AU', $value)) {
            $isValid = false;
        }

        if (!$isValid) {
            $this->context->addViolation($constraint->message);
        }

        return $isValid;
    }
}
