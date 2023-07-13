<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Form\LinePagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\LinePagePart;
use PHPUnit\Framework\TestCase;

class LinePagePartTest extends TestCase
{
    /**
     * @var LinePagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new LinePagePart();
    }

    public function testToString()
    {
        $this->assertSame('LinePagePart', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertSame('@KunstmaanPagePart/LinePagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(LinePagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
