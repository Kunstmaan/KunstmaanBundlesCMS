<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\RemoteVideo;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHandler;
use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHelper;
use PHPUnit\Framework\TestCase;

class RemoteVideoHelperTest extends TestCase
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var RemoteVideoHelper
     */
    protected $object;

    protected function setUp(): void
    {
        $this->media = new Media();
        $this->object = new RemoteVideoHelper($this->media);
    }

    public function testGetMedia()
    {
        $this->assertEquals(RemoteVideoHandler::CONTENT_TYPE, $this->object->getMedia()->getContentType());
    }
}
