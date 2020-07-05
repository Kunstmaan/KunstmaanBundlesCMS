<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsExtensionAllowed extends Constraint
{
    public const NOT_ALLOWED = '6051ebb3-69e1-460a-a032-b33fa42ab4fd';

    protected static $errorNames = [
        self::NOT_ALLOWED => 'NOT_ALLOWED',
    ];

    public $notAllowedMessage = 'media.flash.not_valid';
}
