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
        $this->assertSame(5, $this->object->getMedia()->getId());
        $this->assertSame(5, $this->object->getMedia()->__toString());
        $this->assertSame('https://nasa.gov/spongebob.jpg', $this->object->__toString());
        $pp = new DownloadPagePart();
        $this->assertSame('', $pp->__toString());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertSame('@KunstmaanMediaPagePart/DownloadPagePart/view.html.twig', $defaultView);
    }

    public function testGetAdminView()
    {
        $defaultView = $this->object->getAdminView();
        $this->assertSame('@KunstmaanMediaPagePart/DownloadPagePart/admin-view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(DownloadPagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testEmptyUrl()
    {
        $media = new Media();
        $this->object->setMedia($media);
        $this->assertSame('', $this->object->__toString());
    }
}
