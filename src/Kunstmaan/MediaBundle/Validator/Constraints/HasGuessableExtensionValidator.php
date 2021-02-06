<?php

namespace Kunstmaan\MediaBundle\Validator\Constraints;

use Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactoryInterface;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactoryInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesserInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

class HasGuessableExtensionValidator extends ConstraintValidator
{
    /**
     * @var ExtensionGuesserInterface
     */
    private $extensionGuesser;

    /**
     * @var MimeTypeGuesserInterface
     */
    private $mimeTypeGuesser;

    /** @var MimeTypes */
    private $mimeTypes;

    public function __construct($mimeTypes = null)
    {
        if (null === $mimeTypes) {
            @trigger_error(sprintf('Not passing an instance of "%s" as the first argument of "%s" is deprecated in KunstmaanMediaBundle 5.7 and will be required in KunstmaanMediaBundle 6.0.', MimeTypes::class, __METHOD__), E_USER_DEPRECATED);
        }

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

        $contentType = $this->guessMimeType($value->getPathname());
        $pathInfo = pathinfo($value->getClientOriginalName());
        if (!\array_key_exists('extension', $pathInfo)) {
            $pathInfo['extension'] = $this->getExtension($contentType);
        }

        if ($pathInfo['extension'] === null) {
            $this->context->buildViolation($constraint->notGuessableErrorMessage)
                ->setCode(HasGuessableExtension::NOT_GUESSABLE_ERROR)
                ->addViolation();
        }
    }

    private function guessMimeType($pathName)
    {
        if ($this->mimeTypeGuesser !== null) {
            return $this->mimeTypeGuesser->guess($pathName);
        }

        return $this->mimeTypes->guessMimeType($pathName);
    }

    private function getExtension($mimeType)
    {
        if ($this->extensionGuesser !== null) {
            return $this->extensionGuesser->guess($mimeType);
        }

        return $this->mimeTypes->getExtensions($mimeType)[0] ?? null;
    }

    /**
     * @deprecated This method is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0.
     */
    public function setExtensionGuesser(ExtensionGuesserFactoryInterface $extensionGuesserFactory)
    {
        @trigger_error(sprintf('Calling the "%s" method is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Inject the required services through the constructor instead.', __METHOD__), E_USER_DEPRECATED);

        $this->extensionGuesser = $extensionGuesserFactory->get();
    }

    /**
     * @deprecated This method is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0.
     */
    public function setMimeTypeGuesser(MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory)
    {
        @trigger_error(sprintf('Calling the "%s" method is deprecated since KunstmaanMediaBundle 5.7 and will be removed in KunstmaanMediaBundle 6.0. Inject the required services through the constructor instead.', __METHOD__), E_USER_DEPRECATED);

        $this->mimeTypeGuesser = $mimeTypeGuesserFactory->get();
    }
}
