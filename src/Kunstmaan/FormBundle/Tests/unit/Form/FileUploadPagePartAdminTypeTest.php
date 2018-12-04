<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\PageParts\FileUploadPagePart;
use Kunstmaan\FormBundle\Form\FileUploadPagePartAdminType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class FileUploadPagePartAdminTypeTest
 */
class FileUploadPagePartAdminTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'required' => false,
            'label' => 'xyz',
            'errormessage_required' => 'fill in the form',
        ];

        $form = $this->factory->create(FileUploadPagePartAdminType::class);

        $object = new FileUploadPagePart();
        $object->setRequired(false);
        $object->setErrorMessageRequired('fill in the form');
        $object->setLabel('xyz');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
