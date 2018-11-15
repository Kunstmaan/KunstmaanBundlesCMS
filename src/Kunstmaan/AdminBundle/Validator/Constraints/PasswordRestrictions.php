<?php

namespace Kunstmaan\AdminBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class PasswordRestrictions
 */
class PasswordRestrictions extends Constraint
{
    const INVALID_MIN_DIGITS_ERROR = 1;
    const INVALID_MIN_UPPERCASE_ERROR = 2;
    const INVALID_MIN_SPECIAL_CHARACTERS_ERROR = 3;
    const INVALID_MIN_LENGTH_ERROR = 4;
    const INVALID_MAX_LENGTH_ERROR = 5;

    const MESSAGE_MIN_DIGITS = 'errors.password.mindigits';
    const MESSAGE_MIN_UPPERCASE = 'errors.password.minuppercase';
    const MESSAGE_MIN_SPECIAL_CHARACTERS = 'errors.password.minspecialcharacters';
    const MESSAGE_MIN_LENGTH = 'errors.password.minlength';
    const MESSAGE_MAX_LENGTH = 'errors.password.maxlength';

    /**
     * PasswordRestrictions constructor.
     *
     * @param null $options
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}
