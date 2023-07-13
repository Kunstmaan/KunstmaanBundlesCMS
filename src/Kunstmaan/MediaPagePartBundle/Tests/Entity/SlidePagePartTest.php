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
        $this->assertSame(5, $this->object->getMedia()->getId());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertSame('@KunstmaanMediaPagePart/SlidePagePart/view.html.twig', $defaultView);
    }

    public function testGetAdminView()
    {
        $defaultView = $this->object->getAdminView();
        $this->assertSame('@KunstmaanMediaPagePart/SlidePagePart/admin-view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(SlidePagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testToString()
    {
        $this->assertEmpty($this->object->__toString());
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $this->object->setMedia($media);
        $this->assertSame('https://nasa.gov/spongebob.jpg', $this->object->__toString());
    }

    public function testEmptyUrl()
    {
        $media = new Media();
        $this->object->setMedia($media);
        $this->assertSame('', $this->object->__toString());
    }
}
