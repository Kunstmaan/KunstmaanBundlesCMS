<?php

namespace Kunstmaan\FormBundle\Tests\Validator\Constraints;

use Kunstmaan\FormBundle\Validator\Constraints\AllowedUploadExtension;
use Kunstmaan\FormBundle\Validator\Constraints\AllowedUploadExtensionValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class AllowedUploadExtensionValidatorTest extends ConstraintValidatorTestCase
{
    /** @var string[] */
    private $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];

    protected function createValidator(): AllowedUploadExtensionValidator
    {
        return new AllowedUploadExtensionValidator(new MimeTypes(), $this->allowedExtensions);
    }

    public function testNullIsIgnored()
    {
        $this->validator->validate(null, new AllowedUploadExtension());

        $this->assertNoViolation();
    }

    public function testAllowedImageIsAccepted()
    {
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'example.jpg', null, null, true);

        $this->validator->validate($file, new AllowedUploadExtension());

        $this->assertNoViolation();
    }

    public function testPhpContentIsRejected()
    {
        // The client name says .png, but the content is PHP; validation must reject it.
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/php-shell-fixture', 'shell.png', null, null, true);

        $this->validator->validate($file, new AllowedUploadExtension());

        $this->buildViolation('The type of the uploaded file is not allowed.')
            ->setCode(AllowedUploadExtension::NOT_ALLOWED_ERROR)
            ->assertRaised();
    }

    public function testDisallowedButGuessableTypeIsRejected()
    {
        // A jpg is guessable, but if it is not in the allow-list it must be rejected.
        $validator = new AllowedUploadExtensionValidator(new MimeTypes(), ['pdf']);
        $validator->initialize($this->context);

        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'example.jpg', null, null, true);

        $validator->validate($file, new AllowedUploadExtension());

        $this->buildViolation('The type of the uploaded file is not allowed.')
            ->setCode(AllowedUploadExtension::NOT_ALLOWED_ERROR)
            ->assertRaised();
    }
}
