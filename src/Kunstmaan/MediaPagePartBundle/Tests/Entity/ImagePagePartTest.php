<?php

namespace Kunstmaan\MediaPagePartBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart;
use Kunstmaan\MediaPagePartBundle\Form\ImagePagePartAdminType;
use PHPUnit\Framework\TestCase;

class ImagePagePartTest extends TestCase
{
    /**
     * @var ImagePagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new ImagePagePart();
    }

    public function testGetSetOpenInNewWindow()
    {
        $this->object->setOpenInNewWindow(true);
        $this->assertEquals(true, $this->object->getOpenInNewWindow());
        $this->object->setOpenInNewWindow(false);
        $this->assertEquals(false, $this->object->getOpenInNewWindow());
    }

    public function testSetGetLink()
    {
        $this->object->setLink('abc');
        $this->assertSame('abc', $this->object->getLink());
    }

    public function testSetGetMedia()
    {
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertSame(5, $this->object->getMedia()->getId());
        $this->assertSame(5, $this->object->getMedia()->__toString());
        $this->assertSame('https://nasa.gov/spongebob.jpg', $this->object->__toString());
        $pp = new ImagePagePart();
        $this->assertSame('', $pp->__toString());
    }

    public function testSetGetAlttext()
    {
        $this->object->setAltText('bcd');
        $this->assertSame('bcd', $this->object->getAltText());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertSame('@KunstmaanMediaPagePart/ImagePagePart/view.html.twig', $defaultView);
    }

    public function testGetAdminView()
    {
        $defaultView = $this->object->getAdminView();
        $this->assertSame('@KunstmaanMediaPagePart/ImagePagePart/admin-view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(ImagePagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testEmptyUrl()
    {
        $media = new Media();
        $this->object->setMedia($media);
        $this->assertSame('', $this->object->__toString());
    }
}
