<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart;
use Kunstmaan\FormBundle\Form\MultiLineTextPagePartAdminType;
use Symfony\Component\Form\Test\TypeTestCase;

/**
 * Class MultiLineTextPagePartAdminTypeTest
 */
class MultiLineTextPagePartAdminTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'required' => false,
            'label' => 'type in this box!',
            'errormessage_required' => 'fill in the form',
            'regex' => '#\w+#',
            'errormessage_regex' => 'oops',
        ];

        $form = $this->factory->create(MultiLineTextPagePartAdminType::class);

        $object = new MultiLineTextPagePart();
        $object->setRequired(false);
        $object->setErrorMessageRequired('fill in the form');
        $object->setLabel('type in this box!');
        $object->setRegex('#\w+#');
        $object->setErrorMessageRegex('oops');

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
