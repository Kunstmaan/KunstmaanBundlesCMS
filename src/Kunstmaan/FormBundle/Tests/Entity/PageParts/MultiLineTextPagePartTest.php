<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;

use Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart;
use Kunstmaan\FormBundle\Form\MultiLineTextPagePartAdminType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for MultiLineTextPagePart
 */
class MultiLineTextPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MultiLineTextPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new MultiLineTextPagePart;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::setRegex
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::getRegex
     */
    public function testSetGetRegex()
    {
        $object = $this->object;
        $regex = ".*example.*";
        $object->setRegex($regex);
        $this->assertEquals($regex, $object->getRegex());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::setErrorMessageRegex
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::getErrorMessageRegex
     */
    public function testSetGetErrorMessageRegex()
    {
        $object = $this->object;
        $message = "Some example error message";
        $object->setErrorMessageRegex($message);
        $this->assertEquals($message, $object->getErrorMessageRegex());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::getDefaultView
     */
    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::adaptForm
     */
    public function testAdaptForm()
    {
        $object = $this->object;
        $object->setRequired(true);
        $object->setRegex(".*example.*");

        $formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $formBuilder->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(array()));

        $fields = new ArrayObject();

        $this->assertTrue(count($fields) == 0);
        /* @var $formBuilder FormBuilderInterface */
        $object->adaptForm($formBuilder, $fields, 0);
        $this->assertTrue(count($fields) > 0);
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\MultiLineTextPagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertEquals(MultiLineTextPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
