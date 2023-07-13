<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Form\RawHTMLPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\RawHTMLPagePart;
use PHPUnit\Framework\TestCase;

class RawHTMLPagePartTest extends TestCase
{
    /**
     * @var RawHTMLPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new RawHTMLPagePart();
        $this->object->setContent('<p>test</p>');
    }

    public function testToString()
    {
        $this->assertSame('RawHTMLPagePart ' . htmlentities($this->object->getContent()), $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertSame('@KunstmaanPagePart/RawHTMLPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testSetGetContent()
    {
        $this->object->setContent('tèst content with s3ç!àL');
        $this->assertSame('tèst content with s3ç!àL', $this->object->getContent());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(RawHTMLPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
