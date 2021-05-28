<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExtensionValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate(
        $value,
        Constraint $constraint
    ): void {
        if (!$value instanceof UploadedFile) {
            return;
        }

        if (!$constraint instanceof ExtensionConstraint) {
            throw new InvalidArgumentException('Constraint should be instance of ' . ExtensionConstraint::class);
        }

        $this->validateExtension(
            $value,
            $constraint
        );
    }

    private function validateExtension(
        UploadedFile $file,
        ExtensionConstraint $constraint
    ): void {
        $extension = $this->getExtension($file);

        $isWhitelisted = $this->isWhitelisted(
            $constraint,
            $extension
        );

        if (!$isWhitelisted) {
            $this
                ->context
                ->buildViolation('extension_is_not_allowed.label')
                ->addViolation()
            ;
        }

        $isBlacklisted = $this->isBlacklisted(
            $constraint,
            $extension
        );

        if ($isBlacklisted) {
            $this
                ->context
                ->buildViolation('extension_is_not_allowed.label')
                ->addViolation()
            ;
        }
    }

    private function getExtension(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();

        if (is_string($extension) && $extension !== '') {
            return strtolower($extension);
        }

        return strtolower($file->getExtension());
    }

    private function isWhitelisted(
        ExtensionConstraint $constraint,
        string $extension
    ): bool {
        $whitelistedExtensions = $constraint->whitelistedExtensions;

        if (!count($whitelistedExtensions)) {
            return true;
        }

        return in_array(
            $extension,
            $whitelistedExtensions,
            true
        );
    }

    private function isBlacklisted(
        ExtensionConstraint $constraint,
        string $extension
    ): bool {
        $blacklistedExtensions = $constraint->blacklistedExtensions;

        if (!count($blacklistedExtensions)) {
            return false;
        }

        return in_array(
            $extension,
            $blacklistedExtensions,
            true
        );
    }
}
