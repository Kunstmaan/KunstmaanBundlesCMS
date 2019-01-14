<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart;
use Kunstmaan\FormBundle\Form\EmailPagePartAdminType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class EmailPagePartAdminTypeTest
 */
class EmailPagePartAdminTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'required' => false,
            'errorMessageRequired' => 'required',
            'errorMessageInvalid' => 'invalid',
            'label' => 'label',
        ];

        $form = $this->factory->create(EmailPagePartAdminType::class);

        $object = new EmailPagePart();
        $object->setRequired(false);
        $object->setErrorMessageRequired('required');
        $object->setLabel('label');
        $object->setErrorMessageInvalid('invalid');

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
