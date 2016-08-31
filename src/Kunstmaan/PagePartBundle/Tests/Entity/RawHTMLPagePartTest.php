<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-08-20 at 14:58:05.
 */
class RawHTMLPagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RawHTMLPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new RawHTMLPagePart();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Generated from @assert () == "RawHTMLPagePart " . htmlentities($this->object->getContent()).
     *
     * @covers                Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart::__toString
     */
    public function testToString()
    {
        $this->assertEquals('RawHTMLPagePart '.htmlentities($this->object->getContent()), $this->object->__toString());
    }

    /**
     * Generated from @assert () == 'KunstmaanPagePartBundle:RawHTMLPagePart:view.html.twig'.
     *
     * @covers                Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart::getDefaultView
     */
    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:RawHTMLPagePart:view.html.twig', $this->object->getDefaultView());
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart::setContent
     * @covers Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart::getContent
     */
    public function testSetGetContent()
    {
        $this->object->setContent('tèst content with s3ç!àL');
        $this->assertEquals($this->object->getContent(), 'tèst content with s3ç!àL');
    }

    /**
     * @covers Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart::getDefaultAdminType
     */
    public function testGetDefaultAdminType()
    {
        $this->assertInstanceOf('Kunstmaan\PagePartBundle\Form\RawHTMLPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
