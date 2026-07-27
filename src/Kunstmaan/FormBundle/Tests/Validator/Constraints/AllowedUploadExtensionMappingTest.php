<?php

namespace Kunstmaan\FormBundle\Tests\Validator\Constraints;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Validator\Constraints\AllowedUploadExtension;
use Kunstmaan\FormBundle\Validator\Constraints\AllowedUploadExtensionValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Verifies that the #[AllowedUploadExtension] attribute declared on
 * FileFormSubmissionField::$file is actually picked up through class-metadata
 * validation - i.e. the same cascade the form layer triggers on a public
 * form submission. This is the wiring the isolated validator unit test cannot cover.
 */
class AllowedUploadExtensionMappingTest extends TestCase
{
    private function createValidator(MimeTypesInterface $mimeTypes, array $allowedExtensions): ValidatorInterface
    {
        $factory = new class($mimeTypes, $allowedExtensions) extends ConstraintValidatorFactory {
            private MimeTypesInterface $mimeTypes;

            /** @var string[] */
            private array $allowedExtensions;

            public function __construct(MimeTypesInterface $mimeTypes, array $allowedExtensions)
            {
                parent::__construct();
                $this->mimeTypes = $mimeTypes;
                $this->allowedExtensions = $allowedExtensions;
            }

            public function getInstance(Constraint $constraint): ConstraintValidatorInterface
            {
                if ($constraint instanceof AllowedUploadExtension) {
                    return new AllowedUploadExtensionValidator($this->mimeTypes, $this->allowedExtensions);
                }

                return parent::getInstance($constraint);
            }
        };

        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->setConstraintValidatorFactory($factory)
            ->getValidator();
    }

    public function testPhpUploadIsRejectedThroughClassMetadata()
    {
        $validator = $this->createValidator(new MimeTypes(), ['jpg', 'png', 'pdf']);

        $field = new FileFormSubmissionField();
        $field->file = new UploadedFile(__DIR__ . '/../../Resources/assets/php-shell-fixture', 'shell.php', null, null, true);

        $violations = $validator->validate($field);

        $this->assertGreaterThan(0, $violations->count());
        $this->assertSame(AllowedUploadExtension::NOT_ALLOWED_ERROR, $violations->get(0)->getCode());
    }

    public function testAllowedUploadPassesThroughClassMetadata()
    {
        $validator = $this->createValidator(new MimeTypes(), ['jpg', 'png', 'pdf']);

        $field = new FileFormSubmissionField();
        $field->file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'example.jpg', null, null, true);

        $violations = $validator->validate($field);

        $this->assertCount(0, $violations);
    }
}
