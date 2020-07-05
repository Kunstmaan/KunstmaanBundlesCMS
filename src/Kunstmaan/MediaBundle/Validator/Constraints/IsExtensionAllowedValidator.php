<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Kunstmaan\MediaBundle\Helper\MediaManager;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class IsExtensionAllowedValidator extends ConstraintValidator
{
    /** @var MediaManager */
    private $mediaManager;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(MediaManager $mediaManager, TranslatorInterface $translator)
    {
        $this->mediaManager = $mediaManager;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof IsExtensionAllowed) {
            throw new UnexpectedTypeException($constraint, IsExtensionAllowed::class);
        }

        if (!$value instanceof UploadedFile) {
            return;
        }

        if (!$this->mediaManager->isExtensionAllowed($value)) {
            $this->context->buildViolation($this->translator->trans($constraint->notAllowedMessage))
                ->setCode(IsExtensionAllowed::NOT_ALLOWED)
                ->addViolation();
        }
    }
}
