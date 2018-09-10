<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField;
use Kunstmaan\FormBundle\Form\EmailFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class EmailFormSubmissionTypeTest
 */
class EmailFormSubmissionTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = ['value' => 'delboy1978uk@gmail.com'];

        $form = $this->factory->create(EmailFormSubmissionType::class);
        $field = new EmailFormSubmissionField();
        $field->setValue('delboy1978uk@gmail.com');

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
