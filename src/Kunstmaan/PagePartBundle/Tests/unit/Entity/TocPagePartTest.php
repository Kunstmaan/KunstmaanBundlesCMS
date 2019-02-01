<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\TocPagePart;
use PHPUnit\Framework\TestCase;

class TocPagePartTest extends TestCase
{
    /**
     * @var TocPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new TocPagePart();
    }

    public function testToString()
    {
        $this->assertEquals('TocPagePart', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:TocPagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\TocPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
