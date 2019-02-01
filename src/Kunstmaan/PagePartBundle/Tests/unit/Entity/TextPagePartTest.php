<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\TextPagePart;
use PHPUnit\Framework\TestCase;

class TextPagePartTest extends TestCase
{
    /**
     * @var TextPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TextPagePart();
    }

    public function testSetGetContent()
    {
        $this->object->setContent('tèst content with s3ç!àL');
        $this->assertEquals($this->object->getContent(), 'tèst content with s3ç!àL');
    }

    public function testToString()
    {
        $this->assertEquals('TextPagePart ' . $this->object->getContent(), $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:TextPagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\TextPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
