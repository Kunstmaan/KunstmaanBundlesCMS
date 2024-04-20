<?php

namespace Kunstmaan\NodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\UrlValidator;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidExternalUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!is_string($value)) {
            return;
        }

        if (str_starts_with($value, '#')) {
            return;
        }

        $urlValidator = new UrlValidator();
        $urlValidator->initialize($this->context);
        $options = [];
        if (property_exists(Url::class, 'requireTld')) {
            $options = ['requireTld' => true];
        }
        $urlValidator->validate($value, new Url($options));
    }
}
