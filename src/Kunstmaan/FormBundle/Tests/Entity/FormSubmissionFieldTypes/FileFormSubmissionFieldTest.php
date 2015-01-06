<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Tests for FileFormSubmissionField
 */
class FileFormSubmissionFieldTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileFormSubmissionField
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new FileFormSubmissionField;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField::__toString
     */
    public function test__toString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField::isNull
     */
    public function testIsNull()
    {
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'example.jpg');

        $object = $this->object;
        $this->assertTrue($object->isNull());
        $object->file = $file;
        $this->assertFalse($object->isNull());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField::getSafeFileName
     */
    public function testGetSafeFileName()
    {
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'the file name $@&.jpg');

        $object = $this->object;
        $object->file = $file;
        $safeName = $object->getSafeFileName($file);

        $this->assertEquals('the-file-name.jpeg', $safeName);
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField::setFileName
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField::getFileName
     */
    public function testSetGetFileName()
    {
        $object = $this->object;
        $fileName = 'test.jpg';
        $object->setFileName($fileName);
        $this->assertEquals($fileName, $object->getFileName());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField::getSubmissionTemplate
     */
    public function testGetSubmissionTemplate()
    {
        $template = $this->object->getSubmissionTemplate();
        $this->assertNotNull($template);
    }
}
