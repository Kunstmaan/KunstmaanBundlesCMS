<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsExtensionAllowed extends Constraint
{
    public const NOT_ALLOWED = 1;

    protected static $errorNames = [
        self::NOT_ALLOWED => 'NOT_ALLOWED',
    ];

    public $notAllowedMessage = 'media.flash.not_valid';
}
