<?php

namespace Kunstmaan\AdminBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class PasswordRestrictionsValidator
 */
class PasswordRestrictionsValidator extends ConstraintValidator
{
    /**
     * @var int
     */
    private $minDigits;

    /**
     * @var int
     */
    private $minUppercase;

    /**
     * @var int
     */
    private $minSpecialCharacters;

    /**
     * @var int
     */
    private $minLength;

    /**
     * @var int
     */
    private $maxLength;

    /**
     * PasswordRestrictionsValidator constructor.
     *
     * @param int $minDigits
     * @param int $minUpperCase
     * @param int $minSpecialCharacters
     * @param int $minLength
     * @param int $maxLength
     */
    public function __construct($minDigits, $minUpperCase, $minSpecialCharacters, $minLength, $maxLength)
    {
        $this->minDigits = $minDigits;
        $this->minUppercase = $minUpperCase;
        $this->minSpecialCharacters = $minSpecialCharacters;
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param string     $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PasswordRestrictions) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\PasswordRestrictions');
        }

        if (null === $value) {
            return;
        }

        if (null !== $this->minLength) {
            $this->validateMinLength($value);
        }

        if (null !== $this->maxLength) {
            $this->validateMaxLength($value);
        }

        if (null !== $this->minDigits) {
            $this->validateMinDigits($value);
        }

        if (null !== $this->minUppercase) {
            $this->validateMinUppercase($value);
        }

        if (null !== $this->minSpecialCharacters) {
            $this->validateMinSpecialCharacters($value);
        }
    }

    /**
     * @param string $value
     */
    private function validateMinLength($value)
    {
        if (strlen($value) < $this->minLength) {
            $this->context->buildViolation(PasswordRestrictions::MESSAGE_MIN_LENGTH)
                ->setParameter('{{ min_length }}', $this->minLength)
                ->setCode(PasswordRestrictions::INVALID_MIN_LENGTH_ERROR)
                ->addViolation();
        }
    }

    /**
     * @param string $value
     */
    private function validateMaxLength($value)
    {
        if (strlen($value) > $this->maxLength) {
            $this->context->buildViolation(PasswordRestrictions::MESSAGE_MAX_LENGTH)
                ->setParameter('{{ max_length }}', $this->maxLength)
                ->setCode(PasswordRestrictions::INVALID_MAX_LENGTH_ERROR)
                ->addViolation();
        }
    }

    /**
     * @param string $value
     */
    private function validateMinDigits($value)
    {
        if (preg_match_all('/\d/', $value) < $this->minDigits) {
            $this->context->buildViolation(PasswordRestrictions::MESSAGE_MIN_DIGITS)
                ->setParameter('{{ min_digits }}', $this->minDigits)
                ->setCode(PasswordRestrictions::INVALID_MIN_DIGITS_ERROR)
                ->addViolation();
        }
    }

    /**
     * @param string $value
     */
    private function validateMinUppercase($value)
    {
        if (preg_match_all('/[A-Z]/', $value) < $this->minUppercase) {
            $this->context->buildViolation(PasswordRestrictions::MESSAGE_MIN_UPPERCASE)
                ->setParameter('{{ min_uppercase }}', $this->minUppercase)
                ->setCode(PasswordRestrictions::INVALID_MIN_UPPERCASE_ERROR)
                ->addViolation();
        }
    }

    /**
     * @param string $value
     */
    private function validateMinSpecialCharacters($value)
    {
        if (preg_match_all('/[^a-zA-Z0-9]/', $value) < $this->minSpecialCharacters) {
            $this->context->buildViolation(PasswordRestrictions::MESSAGE_MIN_SPECIAL_CHARACTERS)
                ->setParameter('{{ min_special_characters }}', $this->minSpecialCharacters)
                ->setCode(PasswordRestrictions::INVALID_MIN_SPECIAL_CHARACTERS_ERROR)
                ->addViolation();
        }
    }
}
