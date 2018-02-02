<?php

namespace Tests\Kunstmaan\FormBundle\Entity;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class StringFormSubmissionTypeTest
 * @package Tests\Kunstmaan\FormBundle\Entity
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
    }
}
