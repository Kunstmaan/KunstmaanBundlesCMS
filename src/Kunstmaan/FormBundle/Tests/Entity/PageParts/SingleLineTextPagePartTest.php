<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;
use Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart;
use Kunstmaan\FormBundle\Form\SingleLineTextPagePartAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class SingleLineTextPagePartTest extends TestCase
{
    /**
     * @var SingleLineTextPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new SingleLineTextPagePart();
    }

    public function testSetGetRegex()
    {
        $object = $this->object;
        $regex = '.*example.*';
        $object->setRegex($regex);
        $this->assertEquals($regex, $object->getRegex());
    }

    public function testSetErrorMessageRegex()
    {
        $object = $this->object;
        $message = 'Some example error message';
        $object->setErrorMessageRegex($message);
        $this->assertEquals($message, $object->getErrorMessageRegex());
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testAdaptForm()
    {
        $object = $this->object;
        $object->setRequired(true);
        $object->setRegex('.*example.*');

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder->expects($this->any())
            ->method('getData')
            ->willReturn([]);

        $fields = new ArrayObject();

        $object->setErrorMessageRequired('required');
        $object->setErrorMessageRegex('regex');
        $this->assertEquals(0, count($fields));
        /* @var FormBuilderInterface $formBuilder */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
        $this->assertTrue($object->getRequired());

        $this->assertEquals('required', $object->getErrorMessageRequired());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(SingleLineTextPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
