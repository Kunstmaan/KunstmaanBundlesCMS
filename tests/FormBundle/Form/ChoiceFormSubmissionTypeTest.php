<?php

namespace Tests\Kunstmaan\FormBundle\Entity;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Entity\PageParts\CheckboxPagePart;
use Kunstmaan\FormBundle\Form\BooleanFormSubmissionType;
use Kunstmaan\FormBundle\Form\CheckboxPagePartAdminType;
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
    }
}
