<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\LinkPagePart;
use PHPUnit\Framework\TestCase;

class LinkPagePartTest extends TestCase
{
    /**
     * @var LinkPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new LinkPagePart();
    }

    public function testSetGetUrl()
    {
        $this->object->setUrl('http://www.kunstmaan.be');
        $this->assertSame('http://www.kunstmaan.be', $this->object->getUrl());
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
        $this->assertSame('Some dummy text', $this->object->getText());
    }

    public function testToString()
    {
        $this->assertSame('LinkPagePart', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertSame('@KunstmaanPagePart/LinkPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(LinkPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
