<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;

/**
 * Tests for SubmitButtonPagePart
 */
class SubmitButtonPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SubmitButtonPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new SubmitButtonPagePart;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart::setLabel
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart::getLabel
     */
    public function testSetGetLabel()
    {
        $object = $this->object;
        $label = "Test label";
        $object->setLabel($label);
        $this->assertEquals($label, $object->getLabel());
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart::__toString
     */
    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart::getDefaultView
     */
    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart::getAdminView
     */
    public function testGetAdminView()
    {
        $stringValue = $this->object->getAdminView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    /**
     * @covers Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $adminType = $this->object->getDefaultAdminType();
        $this->assertNotNull($adminType);
        $this->assertTrue($adminType instanceof SubmitButtonPagePartAdminType);
    }
}
