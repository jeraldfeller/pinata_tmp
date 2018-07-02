<?php

namespace Vivo\AddressBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Vivo\AddressBundle\Model\LocalityInterface;
use Vivo\AddressBundle\Repository\SuburbRepositoryInterface;

class LocalityValidator extends ConstraintValidator
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
     * @param LocalityInterface   $value
     * @param Constraint|Locality $constraint
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

        if (!$value instanceof LocalityInterface) {
            throw new UnexpectedTypeException($value, '\Vivo\\AddressBundle\\Model\\LocalityInterface');
        }

        if (!$value->getCountryCode() || !$value->getPostcode() || !$value->getSuburb()) {
            return true;
        }

        $valid = true;
        switch ($value->getCountryCode()) {
            case 'AU':
                if (!$this->suburbRepository->findOneByPostcodeAndSuburb($value->getCountryCode(), $value->getPostcode(), $value->getSuburb())) {
                    $valid = false;
                    $this->context->addViolationAt('suburb', $constraint->invalidSuburbMessage);
                }

                break;

            default:
                if ('' !== $value->getState() && null !== $value->getState()) {
                    $this->context->addViolationAt('state', $constraint->blankStateMessage);
                }

                break;
        }

        return $valid;
    }
}
