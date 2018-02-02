<?php

namespace Tests\Kunstmaan\FormBundle\Entity;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class SubmitButtonPagePartAdminTypeTest
 */
class SubmitButtonPagePartAdminTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'label' => 'Launch!',
        ];

        $form = $this->factory->create(SubmitButtonPagePartAdminType::class);

        $object = new SubmitButtonPagePart();
        $object->setLabel('Launch!');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());

        $this->assertEquals($object, $form->getData());
    }
}
