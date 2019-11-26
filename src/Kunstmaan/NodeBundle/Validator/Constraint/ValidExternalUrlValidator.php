<?php

namespace Kunstmaan\NodeBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Constraints\UrlValidator;
use Symfony\Component\Validator\ConstraintValidator;

final class ValidExternalUrlValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (strpos($value, '#') === 0) {
            return;
        }

        $urlValidator = new UrlValidator();
        $urlValidator->initialize($this->context);
        $urlValidator->validate($value, new Url());
    }
}
