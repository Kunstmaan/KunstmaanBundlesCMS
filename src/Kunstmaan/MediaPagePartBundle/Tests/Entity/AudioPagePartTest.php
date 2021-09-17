<?php

namespace Kunstmaan\MediaPagePartBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Entity\AudioPagePart;
use Kunstmaan\MediaPagePartBundle\Entity\VideoPagePart;
use Kunstmaan\MediaPagePartBundle\Form\AudioPagePartAdminType;
use PHPUnit\Framework\TestCase;

class AudioPagePartTest extends TestCase
{
    /**
     * @var VideoPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new AudioPagePart();
    }

    public function testSetGetMedia()
    {
        $media = new Media();
        $media->setUrl('https://nasa.gov/spongebob.jpg');
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertEquals(5, $this->object->getMedia()->getId());
        $this->assertEquals('https://nasa.gov/spongebob.jpg', $this->object->__toString());
        $pp = new AudioPagePart();
        $this->assertEquals('', $pp->__toString());
    }

    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertEquals('@KunstmaanMediaPagePart/AudioPagePart/view.html.twig', $defaultView);
    }

    public function testGetDefaultAdminType()
    {
        $defaultAdminType = $this->object->getDefaultAdminType();
        $this->assertEquals(AudioPagePartAdminType::class, $defaultAdminType);
    }
}
