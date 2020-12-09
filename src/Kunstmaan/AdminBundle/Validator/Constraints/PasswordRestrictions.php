<?php

namespace Kunstmaan\AdminBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class PasswordRestrictions extends Constraint
{
    public const INVALID_MIN_DIGITS_ERROR = 'af8b48ce-be95-4149-8ab8-f0770536c414';
    public const INVALID_MIN_UPPERCASE_ERROR = '00939a50-4d1c-4cdc-8361-8a5c28ad9c54';
    public const INVALID_MIN_SPECIAL_CHARACTERS_ERROR = '9e905eb6-0ece-4238-8ce9-1eb937280737';
    public const INVALID_MIN_LENGTH_ERROR = '61c8ff3a-027b-449f-ad2d-2c8a3590d778';
    public const INVALID_MAX_LENGTH_ERROR = '4203e839-0e15-4d9a-be26-68adbdc75614';

    public const MESSAGE_MIN_DIGITS = 'errors.password.mindigits';
    public const MESSAGE_MIN_UPPERCASE = 'errors.password.minuppercase';
    public const MESSAGE_MIN_SPECIAL_CHARACTERS = 'errors.password.minspecialcharacters';
    public const MESSAGE_MIN_LENGTH = 'errors.password.minlength';
    public const MESSAGE_MAX_LENGTH = 'errors.password.maxlength';
}
