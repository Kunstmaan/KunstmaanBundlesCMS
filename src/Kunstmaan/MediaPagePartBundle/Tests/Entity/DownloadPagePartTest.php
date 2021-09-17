<?php

namespace Kunstmaan\MediaPagePartBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Entity\DownloadPagePart;
use Kunstmaan\MediaPagePartBundle\Form\DownloadPagePartAdminType;
use PHPUnit\Framework\TestCase;

class DownloadPagePartTest extends TestCase
{
    /**
     * @var DownloadPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new DownloadPagePart();
    }

    public function testGetSetMedia()
    {
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertEquals(5, $this->object->getMedia()->getId());
        $this->assertEquals(5, $this->object->getMedia()->__toString());
        $this->assertEquals('https://nasa.gov/spongebob.jpg', $this->object->__toString());
        $pp = new DownloadPagePart();
        $this->assertEquals('', $pp->__toString());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertEquals('@KunstmaanMediaPagePart/DownloadPagePart/view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(DownloadPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
