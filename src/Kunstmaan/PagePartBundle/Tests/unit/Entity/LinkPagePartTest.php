<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\LinkPagePart;
use PHPUnit_Framework_TestCase;

/**
 * Class LinkPagePartTest
 */
class LinkPagePartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LinkPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new LinkPagePart();
    }

    public function testSetGetUrl()
    {
        $this->object->setUrl('http://www.kunstmaan.be');
        $this->assertEquals('http://www.kunstmaan.be', $this->object->getUrl());
    }

    public function testGetSetOpenInNewWindow()
    {
        $this->object->setOpenInNewWindow(false);
        $this->assertFalse($this->object->getOpenInNewWindow());

        $this->object->setOpenInNewWindow(true);
        $this->assertTrue($this->object->getOpenInNewWindow());
    }

    public function testSetGetText()
    {
        $this->object->setText('Some dummy text');
        $this->assertEquals('Some dummy text', $this->object->getText());
    }

    public function testToString()
    {
        $this->assertEquals('LinkPagePart', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:LinkPagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
