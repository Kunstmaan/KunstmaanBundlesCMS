<?php

namespace Kunstmaan\MediaPagePartBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Entity\SlidePagePart;
use Kunstmaan\MediaPagePartBundle\Form\SlidePagePartAdminType;
use PHPUnit\Framework\TestCase;

class SlidePagePartTest extends TestCase
{
    /**
     * @var SlidePagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new SlidePagePart();
    }

    public function testSetGetMedia()
    {
        $media = new Media();
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertEquals(5, $this->object->getMedia()->getId());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertEquals('@KunstmaanMediaPagePart/SlidePagePart/view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(SlidePagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $this->assertEmpty($this->object->__toString());
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $this->object->setMedia($media);
        $this->assertEquals('https://nasa.gov/spongebob.jpg', $this->object->__toString());
    }
}
