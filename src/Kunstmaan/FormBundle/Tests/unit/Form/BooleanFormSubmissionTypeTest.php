<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\FormBundle\Form\BooleanFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class BooleanFormSubmissionTypeTest
 */
class BooleanFormSubmissionTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = ['value' => true];

        $form = $this->factory->create(BooleanFormSubmissionType::class);
        $field = new BooleanFormSubmissionField();
        $field->setValue(true);

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
