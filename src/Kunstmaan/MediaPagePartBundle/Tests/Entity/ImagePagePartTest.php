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
        $this->assertEquals('abc', $this->object->getLink());
    }

    public function testSetGetMedia()
    {
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertEquals(5, $this->object->getMedia()->getId());
        $this->assertEquals(5, $this->object->getMedia()->__toString());
        $this->assertEquals('https://nasa.gov/spongebob.jpg', $this->object->__toString());
        $pp = new ImagePagePart();
        $this->assertEquals('', $pp->__toString());
    }

    public function testSetGetAlttext()
    {
        $this->object->setAltText('bcd');
        $this->assertEquals('bcd', $this->object->getAltText());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertEquals('@KunstmaanMediaPagePart/ImagePagePart/view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(ImagePagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
