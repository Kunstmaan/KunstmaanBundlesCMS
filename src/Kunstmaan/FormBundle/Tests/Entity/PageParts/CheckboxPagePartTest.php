<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Kunstmaan\FormBundle\Entity\PageParts\CheckboxPagePart;
use Kunstmaan\FormBundle\Form\CheckboxPagePartAdminType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;

class CheckboxPagePartTest extends TestCase
{
    /**
     * @var CheckboxPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new CheckboxPagePart();
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

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder->expects($this->any())
            ->method('getData')
            ->willReturn([]);

        $fields = new \ArrayObject();

        $this->assertEquals(0, count($fields));
        $object->setErrorMessageRequired('omg sort it out');
        /* @var FormBuilderInterface $formBuilder */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    public function testGetDefaultAdminType()
    {
        $adminType = $this->object->getDefaultAdminType();
        $this->assertNotNull($adminType);
        $this->assertEquals(CheckboxPagePartAdminType::class, $adminType);
    }

    public function testErrorMessage()
    {
        $object = $this->object;
        $msg = 'fill in the form properly';
        $object->setErrorMessageRequired($msg);
        $this->assertEquals($msg, $object->getErrorMessageRequired());
    }
}
