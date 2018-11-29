<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileFormSubmissionTypeTest
 */
class FileFormSubmissionTypeTest extends TypeTestCase
{
    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @var UploadedFile
     */
    private $image;

    public function setUp()
    {
        parent::setUp();

        $this->file = tempnam(sys_get_temp_dir(), 'upl'); // create file

        imagepng(imagecreatetruecolor(10, 10), $this->file); // create and write image/png to it

        $this->image = new UploadedFile(
            $this->file,
            'new_image.png'
        );
    }

    public function testFormType()
    {
        $formData = [
            'file' => $this->image,
        ];

        $field = new FileFormSubmissionField();
        $field->file = $this->image;

        $form = $this->factory->create(FileFormSubmissionType::class, $field);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($field, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
