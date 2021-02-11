<?php

namespace Kunstmaan\FormBundle\Tests\Entity;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\FormBundle\Form\AbstractFormPageAdminType;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;

class AbstractFormPageTest extends TestCase
{
    /**
     * @var AbstractFormPage
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = $this->getMockForAbstractClass('Kunstmaan\FormBundle\Entity\AbstractFormPage');
    }

    public function testSetGetThanks()
    {
        $object = $this->object;
        $value = 'thanks';
        $object->setThanks($value);
        $this->assertEquals($value, $object->getThanks());
    }

    public function testSetGetSubject()
    {
        $object = $this->object;
        $value = 'some subject';
        $object->setSubject($value);
        $this->assertEquals($value, $object->getSubject());
    }

    public function testSetGetToEmail()
    {
        $object = $this->object;
        $value = 'example@example.com';
        $object->setToEmail($value);
        $this->assertEquals($value, $object->getToEmail());
    }

    public function testSetGetFromEmail()
    {
        $object = $this->object;
        $value = 'example@example.com';
        $object->setFromEmail($value);
        $this->assertEquals($value, $object->getFromEmail());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(AbstractFormPageAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testGetFormElementsContext()
    {
        $stringValue = $this->object->getFormElementsContext();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testGetControllerAction()
    {
        $action = $this->object->getControllerAction();
        $this->assertEquals('KunstmaanFormBundle:AbstractFormPage:service', $action);
    }

    public function testGenerateThankYouUrl()
    {
        $obj = $this->object;

        $router = $this->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $router->expects($this->any())
            ->method('generate')
            ->willReturn('https://nasa.gov');

        $context = new RenderContext();
        $trans = new NodeTranslation();
        $trans->setLang('en');
        $context['nodetranslation'] = $trans;
        $context['slug'] = 'snail-soup-with-celery';

        $action = $obj->generateThankYouUrl($router, $context);
        $this->assertEquals('https://nasa.gov', $action);
    }
}
