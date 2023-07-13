<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\TextPagePart;
use PHPUnit\Framework\TestCase;

class TextPagePartTest extends TestCase
{
    /**
     * @var TextPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new TextPagePart();
    }

    public function testSetGetContent()
    {
        $this->object->setContent('tèst content with s3ç!àL');
        $this->assertSame('tèst content with s3ç!àL', $this->object->getContent());
    }

    public function testToString()
    {
        $this->assertSame('TextPagePart ' . $this->object->getContent(), $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertSame('@KunstmaanPagePart/TextPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(TextPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
