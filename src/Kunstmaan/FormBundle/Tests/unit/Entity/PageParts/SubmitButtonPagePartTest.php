<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;

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
        $this->object = new SubmitButtonPagePart();
    }

    public function testSetGetLabel()
    {
        $object = $this->object;
        $label = 'Test label';
        $object->setLabel($label);
        $this->assertEquals($label, $object->getLabel());
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    public function testGetAdminView()
    {
        $stringValue = $this->object->getAdminView();
        $this->assertNotNull($stringValue);
        $this->assertTrue(is_string($stringValue));
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(SubmitButtonPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
