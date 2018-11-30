<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart;
use PHPUnit_Framework_TestCase;

/**
 * Class RawHTMLPagePartTest
 */
class RawHTMLPagePartTest extends PHPUnit_Framework_TestCase
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

    public function testToString()
    {
        $this->assertEquals('RawHTMLPagePart ' . htmlentities($this->object->getContent()), $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:RawHTMLPagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testSetGetContent()
    {
        $this->object->setContent('tèst content with s3ç!àL');
        $this->assertEquals($this->object->getContent(), 'tèst content with s3ç!àL');
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\RawHTMLPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
