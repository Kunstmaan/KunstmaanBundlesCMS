<?php

namespace Kunstmaan\FormBundle\Tests\Form;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NonAbstractFormPageAdminType extends AbstractFormPageAdminType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\FormBundle\Tests\Form\FormPage',
        ]);
    }
}

class FormPage extends AbstractFormPage
{
    public function getPossibleChildTypes()
    {
        return null;
    }

    public function getPagePartAdminConfigurations()
    {
        return [
            [
                'name' => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage',
            ],
            [
                'name' => 'FormPage',
                'class' => '{{ namespace }}\Entity\Pages\FormPage',
            ],
        ];
    }

    public function getDefaultView()
    {
        return 'some.twig';
    }
}

class AbstractFormPageAdminTypeTest extends TypeTestCase
{
    public function testFormType()
    {
        $formData = [
            'title' => 'Testing!',
            'thanks' => 'Cheers',
            'subject' => 'Thanks a lot',
            'from_email' => 'delboy1978uk@gmail.com',
            'to_email' => 'iedereen@kunstmaan.be',
        ];
        $form = $this->factory->create(NonAbstractFormPageAdminType::class);

        $formPage = new FormPage();
        $formPage->setTitle('Testing!');
        $formPage->setThanks('<p>Cheers</p>');
        $formPage->setSubject('Thanks a lot');
        $formPage->setFromEmail('delboy1978uk@gmail.com');
        $formPage->setToEmail('iedereen@kunstmaan.be');

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($formPage, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}