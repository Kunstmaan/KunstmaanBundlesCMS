<?php

namespace Kunstmaan\FormBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Validator to check if a file uploaded through a form page file upload file has an extension defined in the allow list.
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class AllowedUploadExtension extends Constraint
{
    public const NOT_ALLOWED_ERROR = '019f9f30-6f47-779f-9cf8-7628e4e750d2';

    protected const ERROR_NAMES = [
        self::NOT_ALLOWED_ERROR => 'NOT_ALLOWED_ERROR',
    ];

    public string $notAllowedErrorMessage = 'The type of the uploaded file is not allowed.';
}
