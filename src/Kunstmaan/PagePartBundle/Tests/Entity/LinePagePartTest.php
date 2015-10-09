<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\LinePagePart;

/**
 * LinePagePartTest
 */
class LinePagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LinePagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new LinePagePart();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\LinePagePart::__toString
     */
    public function testToString()
    {
        $this->assertEquals('LinePagePart', $this->object->__toString());
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\LinePagePart::getDefaultView
     */
    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:LinePagePart:view.html.twig', $this->object->getDefaultView());
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\LinePagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertInstanceOf('Kunstmaan\PagePartBundle\Form\LinePagePartAdminType', $this->object->getDefaultAdminType());
    }

}
