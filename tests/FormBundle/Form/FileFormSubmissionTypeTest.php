<?php

namespace Tests\Kunstmaan\FormBundle\Entity;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField;
use Kunstmaan\FormBundle\Form\EmailFormSubmissionType;
use Kunstmaan\FormBundle\Form\FileFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileFormSubmissionTypeTest
 * @package Tests\Kunstmaan\FormBundle\Entity
 */
class FileFormSubmissionTypeTest extends TypeTestCase
{
//    /**
//     * @var UploadedFile $file
//     */
//    private $file;
//
//    public function setUp()
//    {
//        parent::setUp();
//
//        $this->file = tempnam(sys_get_temp_dir(), 'upl'); // create file
//
//        imagepng(imagecreatetruecolor(10, 10), $this->file); // create and write image/png to it
//
//        $this->image = new UploadedFile(
//            $this->file,
//            'new_image.png'
//        );
//    }
//
//    public function testFormType()
//    {
//        $formData = [
//            'file' => [
//                'label' => 'file',
//                'fieldName' => 'photo',
//                'url' => 'https://cia.gov',
//                'sequence' => 'ABC',
//                'fileName' => 'most-wanted.jpg',
//                'uuid' => '123',
//            ],
//        ];
//
//        $form = $this->factory->create(FileFormSubmissionType::class);
//
//        $field = new FileFormSubmissionField();
//        $field->setLabel('file');
//        $field->setFieldName('photo');
//        $field->setUrl('https://cia.gov');
//        $field->setSequence('ABC');
//        $field->setFileName('most-wanted.jpg');
//        $field->setUuid('123');
//        $field;
//
//        $form->submit($formData);
//
//        $this->assertTrue($form->isSynchronized());
//        $this->assertTrue($form->isValid());
//
//        $this->assertEquals($field, $form->getData());
//    }
}
