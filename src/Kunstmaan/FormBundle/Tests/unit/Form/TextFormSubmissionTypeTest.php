<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\TextFormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class TextFormSubmissionTypeTest
 */
class TextFormSubmissionTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'value' => 'ball of string',
        ];

        $form = $this->factory->create(TextFormSubmissionType::class);

        $object = new TextFormSubmissionField();
        $object->setValue('ball of string');

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
