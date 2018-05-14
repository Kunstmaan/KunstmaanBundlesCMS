<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HasGuessableExtension extends Constraint
{
    const NOT_GUESSABLE_ERROR = 1;

    protected static $errorNames = array(
        self::NOT_GUESSABLE_ERROR => 'NOT_GUESSABLE_ERROR',
    );

    public $notGuessableErrorMessage = 'The uploaded file has no extension and could not be automatically guessed by the system.';

    public function __construct($options = null)
    {
        parent::__construct($options);
    }
}
