<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\ToTopPagePart;
use PHPUnit\Framework\TestCase;

class ToTopPagePartTest extends TestCase
{
    /**
     * @var ToTopPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ToTopPagePart();
    }

    /**
     * Generated from @assert () == 'ToTopPagePart'.
     */
    public function testToString()
    {
        $this->assertSame('ToTopPagePart', $this->object->__toString());
    }

    /**
     * Generated from @assert () == 'KunstmaanPagePartBundle:ToTopPagePart:view.html.twig'.
     */
    public function testGetDefaultView()
    {
        $this->assertSame('@KunstmaanPagePart/ToTopPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(ToTopPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
