<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class HasGuessableExtensionValidator extends ConstraintValidator
{
    /** @var MimeTypes */
    private $mimeTypes;

    public function __construct(MimeTypesInterface $mimeTypes)
    {
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @throws ConstraintDefinitionException
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof HasGuessableExtension) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__ . '\HasGuessableExtension');
        }

        if (!$value instanceof UploadedFile) {
            return;
        }

        $contentType = $this->mimeTypes->guessMimeType($value->getPathname());
        $pathInfo = pathinfo($value->getClientOriginalName());
        if (!\array_key_exists('extension', $pathInfo)) {
            $pathInfo['extension'] = $this->mimeTypes->getExtensions($contentType)[0] ?? null;
        }

        if ($pathInfo['extension'] === null) {
            $this->context->buildViolation($constraint->notGuessableErrorMessage)
                ->setCode(HasGuessableExtension::NOT_GUESSABLE_ERROR)
                ->addViolation();
        }
    }
}
