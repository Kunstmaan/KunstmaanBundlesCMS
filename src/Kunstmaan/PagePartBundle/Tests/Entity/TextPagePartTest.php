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

    protected function setUp(): void
    {
        $this->object = new TextPagePart();
    }

    public function testSetGetContent()
    {
        $this->object->setContent('tèst content with s3ç!àL');
        $this->assertEquals('tèst content with s3ç!àL', $this->object->getContent());
    }

    public function testToString()
    {
        $this->assertEquals('TextPagePart ' . $this->object->getContent(), $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('@KunstmaanPagePart/TextPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\TextPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
