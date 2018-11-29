<?php

namespace Kunstmaan\MediaBundle\Tests\Validator\Constraints;

use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictions;
use Kunstmaan\AdminBundle\Validator\Constraints\PasswordRestrictionsValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

/**
 * Class PasswordRestrictionsValidatorTest
 *
 * Unit test for the password restrictions validator, will test with different sets of parameters
 * for the validator is possible.
 */
class PasswordRestrictionsValidatorTest extends ConstraintValidatorTestCase
{
    const PARAMETER_MIN_LENGTH = 8;
    const PARAMETER_MAX_LENGTH = 16;
    const PARAMETER_MIN_DIGITS = 3;
    const PARAMETER_MIN_UPPERCASE = 2;
    const PARAMETER_MIN_SPECIAL_CHARACTERS = 1;

    /**
     * @return PasswordRestrictionsValidator
     */
    protected function createValidator()
    {
        return new PasswordRestrictionsValidator(self::PARAMETER_MIN_DIGITS, self::PARAMETER_MIN_UPPERCASE, self::PARAMETER_MIN_SPECIAL_CHARACTERS, self::PARAMETER_MIN_LENGTH, self::PARAMETER_MAX_LENGTH);
    }

    /**
     * Set a validator with a limited number of parameters to overrule the default one from createValidator.
     *
     * @param int $minDigits
     * @param int $minUppercase
     * @param int $minSpecialCharacters
     * @param int $minLength
     * @param int $maxLength
     */
    protected function setValidator($minDigits, $minUppercase, $minSpecialCharacters, $minLength, $maxLength)
    {
        $this->validator = new PasswordRestrictionsValidator($minDigits, $minUppercase, $minSpecialCharacters, $minLength, $maxLength);
        $this->validator->initialize($this->context);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsWithAllParameters
     */
    public function testPasswordWithAllParametersSet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsToShort
     */
    public function testPasswordToShortOnlySet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->setValidator(null, null, null, self::PARAMETER_MIN_LENGTH, null);
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsToLong
     */
    public function testPasswordToLongOnlySet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->setValidator(null, null, null, null, self::PARAMETER_MAX_LENGTH);
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsMinimumDigits
     */
    public function testPasswordMinimumDigitsOnlySet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->setValidator(self::PARAMETER_MIN_DIGITS, null, null, null, null);
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsMinimumUppercase
     */
    public function testPasswordMinimumUppercaseOnlySet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->setValidator(null, self::PARAMETER_MIN_UPPERCASE, null, null, null);
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsMinimumSpecialCharacters
     */
    public function testPasswordMinimumSpecialCharactersOnlySet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->setValidator(null, null, self::PARAMETER_MIN_SPECIAL_CHARACTERS, null, null);
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     *
     * @dataProvider dataPasswordsLengthRange
     */
    public function testPasswordLengthRangeSet($password, $message = null, array $parameters = [], $code = null)
    {
        $this->setValidator(null, null, null, self::PARAMETER_MIN_LENGTH, self::PARAMETER_MAX_LENGTH);
        $this->buildAndTestPasswordRestrictions($password, $message, $parameters, $code);
    }

    /**
     * Uses the set validator combined with data to assert.
     *
     * @param string      $password
     * @param null|string $message
     * @param array       $parameters
     * @param null        $code
     */
    private function buildAndTestPasswordRestrictions($password, $message = null, array $parameters = [], $code = null)
    {
        $constraint = new PasswordRestrictions();

        $this->validator->validate($password, $constraint);

        if ($message && $code) {
            $this->buildViolation($message)
                ->setCode($code)
                ->setParameters($parameters)
                ->assertRaised();
        } else {
            $this->assertNoViolation();
        }
    }

    /**
     * @return array
     */
    public function dataPasswordsWithAllParameters()
    {
        return [
            ['ABcdef789!'],
            ['AB123!q', PasswordRestrictions::MESSAGE_MIN_LENGTH, ['{{ min_length }}' => self::PARAMETER_MIN_LENGTH], PasswordRestrictions::INVALID_MIN_LENGTH_ERROR],
            ['AB123!q541kkjhghvhb451', PasswordRestrictions::MESSAGE_MAX_LENGTH, ['{{ max_length }}' => self::PARAMETER_MAX_LENGTH], PasswordRestrictions::INVALID_MAX_LENGTH_ERROR],
            ['abCDEFG*', PasswordRestrictions::MESSAGE_MIN_DIGITS, ['{{ min_digits }}' => self::PARAMETER_MIN_DIGITS], PasswordRestrictions::INVALID_MIN_DIGITS_ERROR],
            ['ab123efg!', PasswordRestrictions::MESSAGE_MIN_UPPERCASE, ['{{ min_uppercase }}' => self::PARAMETER_MIN_UPPERCASE], PasswordRestrictions::INVALID_MIN_UPPERCASE_ERROR],
            ['AB123efg', PasswordRestrictions::MESSAGE_MIN_SPECIAL_CHARACTERS, ['{{ min_special_characters }}' => self::PARAMETER_MIN_SPECIAL_CHARACTERS], PasswordRestrictions::INVALID_MIN_SPECIAL_CHARACTERS_ERROR],
        ];
    }

    /**
     * @return array
     */
    public function dataPasswordsToShort()
    {
        return [
            ['password'],
            ['ABC?123', PasswordRestrictions::MESSAGE_MIN_LENGTH, ['{{ min_length }}' => self::PARAMETER_MIN_LENGTH], PasswordRestrictions::INVALID_MIN_LENGTH_ERROR],
            ['admin', PasswordRestrictions::MESSAGE_MIN_LENGTH, ['{{ min_length }}' => self::PARAMETER_MIN_LENGTH], PasswordRestrictions::INVALID_MIN_LENGTH_ERROR],
        ];
    }

    /**
     * @return array
     */
    public function dataPasswordsToLong()
    {
        return [
            ['correct!'],
            ['thispasswordistolong', PasswordRestrictions::MESSAGE_MAX_LENGTH, ['{{ max_length }}' => self::PARAMETER_MAX_LENGTH], PasswordRestrictions::INVALID_MAX_LENGTH_ERROR],
        ];
    }

    /**
     * @return array
     */
    public function dataPasswordsMinimumDigits()
    {
        return [
            ['withdigits123'],
            ['nodigits', PasswordRestrictions::MESSAGE_MIN_DIGITS, ['{{ min_digits }}' => self::PARAMETER_MIN_DIGITS], PasswordRestrictions::INVALID_MIN_DIGITS_ERROR],
        ];
    }

    /**
     * @return array
     */
    public function dataPasswordsMinimumUppercase()
    {
        return [
            ['PassworD'],
            ['password', PasswordRestrictions::MESSAGE_MIN_UPPERCASE, ['{{ min_uppercase }}' => self::PARAMETER_MIN_UPPERCASE], PasswordRestrictions::INVALID_MIN_UPPERCASE_ERROR],
        ];
    }

    /**
     * @return array
     */
    public function dataPasswordsMinimumSpecialCharacters()
    {
        return [
            ['password!'],
            ['password', PasswordRestrictions::MESSAGE_MIN_SPECIAL_CHARACTERS, ['{{ min_special_characters }}' => self::PARAMETER_MIN_SPECIAL_CHARACTERS], PasswordRestrictions::INVALID_MIN_SPECIAL_CHARACTERS_ERROR],
        ];
    }

    /**
     * Combine the data of the too long and too short test for an extra length
     * range test.
     *
     * @return array
     */
    public function dataPasswordsLengthRange()
    {
        return $this->dataPasswordsToLong() + $this->dataPasswordsToShort();
    }
}
