<?php

namespace Kunstmaan\MediaBundle\Tests\Validator\Constraints;

use Kunstmaan\MediaBundle\Entity\Media as MediaObject;
use Kunstmaan\MediaBundle\Validator\Constraints\Media;
use Kunstmaan\MediaBundle\Validator\Constraints\MediaValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class MediaValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new MediaValidator();
    }

    public function testMimeTypeIsIgnoredWhenNotSpecified()
    {
        $constraint = new Media();
        $media = new MediaObject();
        $media->setContentType('image/jpeg');

        $this->validator->validate($media, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @dataProvider dataMimeTypes
     */
    public function testMimeTypeMatches($contentType, $allowed, $message = null, array $parameters = [], $code = null)
    {
        $constraint = new Media(['mimeTypes' => $allowed]);
        $media = (new MediaObject())->setContentType($contentType);

        $this->validator->validate($media, $constraint);

        if ($message && $code) {
            $this->buildViolation($message)
                ->setCode($code)
                ->setParameters($parameters)
                ->assertRaised();
        } else {
            $this->assertNoViolation();
        }
    }

    public function testSvgIsNotTestedForDimensions()
    {
        $constraint = new Media(['minHeight' => 100]);
        $media = (new MediaObject())->setContentType('image/svg+xml');

        $this->validator->validate($media, $constraint);

        $this->assertNoViolation();
    }

    /**
     * @param string $dimension
     * @param int    $value
     * @param string $message
     * @param int    $code
     *
     * @dataProvider dataDimensions
     */
    public function testDimensionsAreChecked($dimension, $value, $message = null, array $parameters = [], $code = null)
    {
        $constraint = new Media([$dimension => $value]);
        $media = (new MediaObject())
            ->setMetadataValue('original_width', 100)
            ->setMetadataValue('original_height', 100)
            ->setContentType('image/png');

        $this->validator->validate($media, $constraint);

        if ($message && $code) {
            $this->buildViolation($message)
                ->setCode($code)
                ->setParameters($parameters)
                ->assertRaised();
        } else {
            $this->assertNoViolation();
        }
    }

    public function dataMimeTypes(): \Iterator
    {
        yield ['image/png', ['image/png']];
        yield ['image/png', ['image/jpg', 'image/png']];
        yield ['image/png', ['image/*']];
        yield ['image/PNG', ['image/png']];
        yield ['image/png', ['image/PNG']];
        yield ['image/png', ['image/jpg'], 'The type of the file is invalid ({{ type }}). Allowed types are {{ types }}.', ['{{ type }}' => '"image/png"', '{{ types }}' => '"image/jpg"'], Media::INVALID_MIME_TYPE_ERROR];
        yield ['image/png', ['application/*'], 'The type of the file is invalid ({{ type }}). Allowed types are {{ types }}.', ['{{ type }}' => '"image/png"', '{{ types }}' => '"application/*"'], Media::INVALID_MIME_TYPE_ERROR];
    }

    public function dataDimensions(): \Iterator
    {
        // image size is 100x100
        yield ['minHeight', 100];
        yield ['maxHeight', 100];
        yield ['minWidth', 100];
        yield ['maxWidth', 100];
        yield ['minWidth', 200, 'The image width is too small ({{ width }}px). Minimum width expected is {{ min_width }}px.', ['{{ width }}' => 100, '{{ min_width }}' => 200], Media::TOO_NARROW_ERROR];
        yield ['maxWidth', 50, 'The image width is too big ({{ width }}px). Allowed maximum width is {{ max_width }}px.', ['{{ width }}' => 100, '{{ max_width }}' => 50], Media::TOO_WIDE_ERROR];
        yield ['minHeight', 200, 'The image height is too small ({{ height }}px). Minimum height expected is {{ min_height }}px.', ['{{ height }}' => 100, '{{ min_height }}' => 200], Media::TOO_LOW_ERROR];
        yield ['maxHeight', 50, 'The image height is too big ({{ height }}px). Allowed maximum height is {{ max_height }}px.', ['{{ height }}' => 100, '{{ max_height }}' => 50], Media::TOO_HIGH_ERROR];
    }
}
