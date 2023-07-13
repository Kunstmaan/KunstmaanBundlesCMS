<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Form\TocPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\TocPagePart;
use PHPUnit\Framework\TestCase;

class TocPagePartTest extends TestCase
{
    /**
     * @var TocPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new TocPagePart();
    }

    public function testToString()
    {
        $this->assertSame('TocPagePart', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertSame('@KunstmaanPagePart/TocPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(TocPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
