<?php

namespace Kunstmaan\MediaPagePartBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Entity\VideoPagePart;
use Kunstmaan\MediaPagePartBundle\Form\VideoPagePartAdminType;
use PHPUnit\Framework\TestCase;

class VideoPagePartTest extends TestCase
{
    /**
     * @var VideoPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new VideoPagePart();
    }

    public function testSetGetMedia()
    {
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertSame(5, $this->object->getMedia()->getId());
        $this->assertSame('https://nasa.gov/spongebob.jpg', $this->object->__toString());
        $pp = new VideoPagePart();
        $this->assertSame('', $pp->__toString());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertSame('@KunstmaanMediaPagePart/VideoPagePart/view.html.twig', $defaultView);
    }

    public function testGetAdminView()
    {
        $defaultView = $this->object->getAdminView();
        $this->assertSame('@KunstmaanMediaPagePart/VideoPagePart/admin-view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertSame(VideoPagePartAdminType::class, $this->object->getDefaultAdminType());
    }

    public function testEmptyMediaUrl()
    {
        $media = new Media();
        $this->object->setMedia($media);
        $this->assertSame('', $this->object->__toString());
    }
}
