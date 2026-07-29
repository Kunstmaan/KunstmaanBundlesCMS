<?php

namespace Kunstmaan\FormBundle\Tests\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests for FileFormSubmissionField
 */
class FileFormSubmissionFieldTest extends TestCase
{
    /**
     * @var FileFormSubmissionField
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new FileFormSubmissionField();
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
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

        $this->assertSame('the-file-name.jpg', $safeName);
    }

    public function testGetSafeFileNameUsesContentExtensionNotClientName()
    {
        // A jpg uploaded with a malicious .php client name must be stored as .jpg,
        // never as .php. The stored extension is derived from the file content.
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'evil.php');

        $object = $this->object;
        $object->file = $file;

        $this->assertSame('evil.jpg', $object->getSafeFileName());
    }

    public function testGetSafeFileNameRejectsUnguessablePhpContent()
    {
        // PHP content is not guessable to a safe extension, so it must be refused
        // outright instead of falling back to the client-supplied ".php" extension.
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/php-shell-fixture', 'shell.php');

        $object = $this->object;
        $object->file = $file;

        // Sanity check: the exploit relies on PHP content having no guessable extension.
        $this->assertNull($file->guessExtension());

        $this->expectException(FileException::class);
        $this->expectExceptionMessage('The type of the uploaded file could not be determined and is therefore not allowed.');

        $object->getSafeFileName();
    }

    public function testGetSafeFileNameEnforcesAllowListAtStorageLayer()
    {
        // Even if the validation constraint were bypassed, a guessable but disallowed
        // type must never be written to disk.
        $file = new UploadedFile(__DIR__ . '/../../Resources/assets/example.jpg', 'example.jpg');

        $object = $this->object;
        $object->file = $file;

        // jpg is guessable and would normally be accepted ...
        $this->assertSame('example.jpg', $object->getSafeFileName(['jpg', 'png']));

        // ... but not when it is absent from the allow-list.
        $this->expectException(FileException::class);
        $object->getSafeFileName(['pdf']);
    }

    public function testGettersAndSetters()
    {
        $object = $this->object;
        $fileName = 'test.jpg';
        $object->setFileName($fileName);
        $object->setUrl('https://nasa.gov');
        $object->setUuid('123');

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

        $file->method('getClientOriginalName')->willReturn('example-name.pdf');
        $file->method('guessExtension')->willReturn('pdf');

        $file->expects($this->any())->method('move');

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
            ->willReturn('whatever');

        $object->onValidPost($form, $builder, $request, $container);
    }
}
