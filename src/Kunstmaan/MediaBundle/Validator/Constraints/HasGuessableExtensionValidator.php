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

/**
 * Class hasGuessableExtensionValidator
 */
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
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @param            $value
     * @param Constraint $constraint
     *
     * @throws ConstraintDefinitionException
     * @throws UnexpectedTypeException
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof HasGuessableExtension) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\HasGuessableExtension');
        }

        if (!$value instanceof UploadedFile) {
            return;
        }

        $contentType = $this->guessMimeType($value->getPathname());
        $pathInfo = pathinfo($value->getClientOriginalName());
        if (!\array_key_exists('extension', $pathInfo)) {
            $pathInfo['extension'] = $this->getExtensions($contentType);
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

    private function getExtensions($mimeType)
    {
        if ($this->extensionGuesser !== null) {
            return $this->extensionGuesser->guess($mimeType);
        }

        return $this->mimeTypes->getExtensions($mimeType)[0] ?? null;
    }

    /**
     * @param ExtensionGuesserFactoryInterface $extensionGuesserFactory
     */
    public function setExtensionGuesser(ExtensionGuesserFactoryInterface $extensionGuesserFactory)
    {
        @trigger_error('Calling the setExtensionGuesser method of this service is deprecated since KunstmaanMediaBundle 5.6 and will be replaced by the "@mime.types" service in KunstmaanMediaBundle 6.0.', E_USER_DEPRECATED);
        $this->extensionGuesser = $extensionGuesserFactory->get();
    }

    /**
     * @param MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory
     */
    public function setMimeTypeGuesser(MimeTypeGuesserFactoryInterface $mimeTypeGuesserFactory)
    {
        @trigger_error('Calling the "setMimeTypeGuesser" method on "\Kunstmaan\MediaBundle\Validator\Constraints\ HasGuessableExtensionValidator" is deprecated since KunstmaanMediaBundle 5.6 and the method will be removed in KunstmaanMediaBundle 6.0. Inject the required services through the constructor instead.', E_USER_DEPRECATED);
        $this->mimeTypeGuesser = $mimeTypeGuesserFactory->get();
    }
}
