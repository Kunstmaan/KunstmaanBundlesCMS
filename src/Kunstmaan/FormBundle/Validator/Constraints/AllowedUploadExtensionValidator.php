<?php

namespace Kunstmaan\FormBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class AllowedUploadExtensionValidator extends ConstraintValidator
{
    private MimeTypesInterface $mimeTypes;

    /** @var string[] */
    private array $allowedExtensions;

    /**
     * @param string[] $allowedExtensions
     */
    public function __construct(MimeTypesInterface $mimeTypes, array $allowedExtensions)
    {
        $this->mimeTypes = $mimeTypes;
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AllowedUploadExtension) {
            throw new UnexpectedTypeException($constraint, AllowedUploadExtension::class);
        }

        if (!$value instanceof UploadedFile) {
            return;
        }

        // Derive the extension from the file content, exactly like the extension
        // the file will ultimately be stored under. Never trust the client name.
        $contentType = $this->mimeTypes->guessMimeType($value->getPathname());
        $extension = null !== $contentType ? ($this->mimeTypes->getExtensions($contentType)[0] ?? null) : null;

        if (null === $extension || !\in_array(strtolower($extension), $this->allowedExtensions, true)) {
            $this->context->buildViolation($constraint->notAllowedErrorMessage)
                ->setCode(AllowedUploadExtension::NOT_ALLOWED_ERROR)
                ->addViolation();
        }
    }
}
