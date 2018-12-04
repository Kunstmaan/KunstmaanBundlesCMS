<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class ChoiceFormSubmissionTypeTest
 */
class ChoiceFormSubmissionTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'value' => 'beer',
        ];

        $form = $this->factory->create(ChoiceFormSubmissionType::class, null, ['choices' => [
            'beer' => 'bier',
            'wine' => 'wijn',
        ]]);

        $object = new ChoiceFormSubmissionField();
        $object->setValue('beer');

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
