<?php

namespace Tests\Kunstmaan\FormBundle\Entity;

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
    }
}
