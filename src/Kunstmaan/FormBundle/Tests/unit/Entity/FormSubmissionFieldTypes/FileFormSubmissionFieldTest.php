<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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
        $this->object = new FileFormSubmissionField();
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    public function testIsNull()
    {
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'example.jpg');

        $object = $this->object;
        $this->assertTrue($object->isNull());
        $object->file = $file;
        $this->assertFalse($object->isNull());
    }

    public function testGetSafeFileName()
    {
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'the file name $@&.jpg');

        $object = $this->object;
        $object->file = $file;
        $safeName = $object->getSafeFileName();

        $this->assertEquals('the-file-name.jpeg', $safeName);
    }

    public function testGettersAndSetters()
    {
        $object = $this->object;
        $fileName = 'test.jpg';
        $object->setFileName($fileName);
        $object->setUrl('https://nasa.gov');
        $object->setuuid('123');

        $this->assertEquals($fileName, $object->getFileName());
        $this->assertEquals('https://nasa.gov', $object->getUrl());
        $this->assertEquals('123', $object->getUuid());
        $this->assertEquals(FileFormSubmissionType::class, $object->getDefaultAdminType());
    }

    public function testGetSubmissionTemplate()
    {
        $template = $this->object->getSubmissionTemplate();
        $this->assertNotNull($template);
    }

    public function testUpload()
    {
        $object = $this->object;
        $this->assertNull($object->upload('..', '..'));

        $file = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $file->expects($this->any())
            ->method('move')
            ->will($this->returnValue(true));

        $object->file = $file;
        $object->upload(__DIR__ . '/../../Resources/assets/', __DIR__ . '/../../Resources/assets/');

        $form = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = new Request();

        $container = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->any())
            ->method('getParameter')
            ->will($this->returnValue('whatever'));

        $object->onValidPost($form, $builder, $request, $container);
    }
}
