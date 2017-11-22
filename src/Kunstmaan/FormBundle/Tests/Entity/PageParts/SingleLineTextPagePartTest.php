<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use ArrayObject;

use Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart;
use Kunstmaan\FormBundle\Form\SingleLineTextPagePartAdminType;

use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tests for SingleLineTextPagePart
 */
class SingleLineTextPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SingleLineTextPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SingleLineTextPagePart;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::setRegex
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::getRegex
     */
    public function testSetGetRegex()
    {
        $object = $this->object;
        $regex = ".*example.*";
        $object->setRegex($regex);
        $this->assertEquals($regex, $object->getRegex());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::setErrorMessageRegex
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::getErrorMessageRegex
     */
    public function testSetErrorMessageRegex()
    {
        $object = $this->object;
        $message = "Some example error message";
        $object->setErrorMessageRegex($message);
        $this->assertEquals($message, $object->getErrorMessageRegex());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::getDefaultView
     */

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::adaptForm
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
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SingleLineTextPagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $adminType = $this->object->getDefaultAdminType();
        $this->assertNotNull($adminType);
        $this->assertTrue($adminType instanceof SingleLineTextPagePartAdminType);
    }
}
