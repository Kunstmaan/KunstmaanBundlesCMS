<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class HasGuessableExtension extends Constraint
{
    public const NOT_GUESSABLE_ERROR = '496c3e10-17a0-4bb0-b3ad-c2bf99731703';

    protected const ERROR_NAMES = [
        self::NOT_GUESSABLE_ERROR => 'NOT_GUESSABLE_ERROR',
    ];

    public $notGuessableErrorMessage = 'The uploaded file has no extension and could not be automatically guessed by the system.';
}
