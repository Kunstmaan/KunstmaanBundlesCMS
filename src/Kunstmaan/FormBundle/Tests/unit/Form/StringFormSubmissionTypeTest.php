<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class StringFormSubmissionTypeTest
 */
class StringFormSubmissionTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'value' => 'ball of string',
        ];

        $form = $this->factory->create(StringFormSubmissionType::class);

        $object = new StringFormSubmissionField();
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
