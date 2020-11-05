<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\RemoteSlide;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHandler;
use Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHelper;
use PHPUnit\Framework\TestCase;

class RemoteSlideHelperTest extends TestCase
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var RemoteSlideHelper
     */
    protected $object;

    protected function setUp()
    {
        $this->media = new Media();
        $this->object = new RemoteSlideHelper($this->media);
    }

    public function testGetMedia()
    {
        $this->assertEquals(RemoteSlideHandler::CONTENT_TYPE, $this->object->getMedia()->getContentType());
    }
}
