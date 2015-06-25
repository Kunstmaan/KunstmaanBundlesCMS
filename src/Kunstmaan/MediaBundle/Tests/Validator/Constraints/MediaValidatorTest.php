<?php

namespace Kunstmaan\MediaBundle\Tests\Validator\Constraints;

use Kunstmaan\MediaBundle\Entity\Media as MediaObject;
use Kunstmaan\MediaBundle\Validator\Constraints\Media;
use Kunstmaan\MediaBundle\Validator\Constraints\MediaValidator;
use Symfony\Component\Validator\ExecutionContextInterface;

class MediaValidatorTest extends \PHPUnit_Framework_TestCase
{


    public function testMimeTypeIsIgnoredWhenNotSpecified()
    {
        $constraint = new Media();
        $media = new MediaObject;

        $validator = $this->getValidator($this->noViolation());

        $validator->validate($media, $constraint);
        // no exception
    }

    /**
     * @param $contentType
     * @param $allowed
     * @param $message
     * @param $code
     *
     * @dataProvider dataMimeTypes
     */
    public function testMimeTypeMatches($contentType, $allowed, $message = null, $code = null)
    {
        $constraint = new Media(['mimeTypes' => $allowed]);
        $media = (new MediaObject)->setContentType($contentType);

        $validator = $this->getValidator(($message && $code) ? $this->thisViolation($message, $code) : $this->noViolation());

        $validator->validate($media, $constraint);

        // expect
    }

    public function testSvgIsNotTestedForDimensions()
    {
        $constraint = new Media(['minHeight' => 100]);
        $media = (new MediaObject)->setContentType('image/svg+xml');

        $validator = $this->getValidator($this->noViolation());
        $validator->validate($media, $constraint);

        // no violation
    }

    /**
     * @param string $dimension
     * @param int    $value
     * @param string $message
     * @param int    $code
     *
     * @dataProvider dataDimensions
     */
    public function testDimensionsAreChecked($dimension, $value, $message = null, $code = null)
    {
        $constraint = new Media([$dimension => $value]);
        $media = (new MediaObject)
            ->setMetadataValue('original_width', 100)
            ->setMetadataValue('original_height', 100)
            ->setContentType('image/png');

        $validator = $this->getValidator(($message && $code) ? $this->thisViolation($message, $code) : $this->noViolation());

        $validator->validate($media, $constraint);

        // expect
    }

    public function dataMimeTypes()
    {
        $errors = new Media;

        return [
            ['image/png', ['image/png']],
            ['image/png', ['image/jpg', 'image/png']],
            ['image/png', ['image/*']],
            ['image/PNG', ['image/png']],
            ['image/png', ['image/PNG']],
            ['image/png', ['image/jpg'], $errors->mimeTypesMessage, $errors::INVALID_MIME_TYPE_ERROR],
            ['image/png', ['application/*'], $errors->mimeTypesMessage, $errors::INVALID_MIME_TYPE_ERROR],
        ];
    }

    public function dataDimensions()
    {
        $errors = new Media;


        // image size is 100x100
        return [
            ['minHeight', 100],
            ['maxHeight', 100],
            ['minWidth', 100],
            ['maxWidth', 100],
            ['minWidth', 200, $errors->minWidthMessage, $errors::TOO_NARROW_ERROR],
            ['maxWidth', 50, $errors->maxWidthMessage, $errors::TOO_WIDE_ERROR],
            ['minHeight', 200, $errors->minHeightMessage, $errors::TOO_LOW_ERROR],
            ['maxHeight', 50, $errors->maxHeightMessage, $errors::TOO_HIGH_ERROR],
        ];
    }

    private function getValidator(callable $mockCallback = null)
    {
        $builder = $this->getMockBuilder('\Symfony\Component\Validator\ExecutionContextInterface');

        /** @var ExecutionContextInterface $mock */
        $mock = $builder->getMock();
        $mockCallback && $mockCallback($mock);

        $validator = new MediaValidator();
        $validator->initialize($mock);

        return $validator;
    }

    private function thisViolation($message, $code)
    {
        return function (\PHPUnit_Framework_MockObject_MockObject $mock) use ($message, $code) {
            $mock->expects($this->once())->method('addViolation')->with(
                $this->equalTo($message), $this->anything(), $this->anything(), $this->anything(), $this->equalTo($code)
            );
        };
    }

    private function noViolation()
    {
        return function (\PHPUnit_Framework_MockObject_MockObject $mock) {
            $mock->expects($this->never())->method('addViolation');
        };
    }
}
