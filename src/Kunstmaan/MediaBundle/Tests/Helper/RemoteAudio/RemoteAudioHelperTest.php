<?php

namespace Kunstmaan\MediaBundle\Tests\Helper\RemoteAudio;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHandler;
use Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHelper;

class RemoteAudioHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Media
     */
    protected $media;

    /**
     * @var RemoteAudioHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @covers Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHelper::__construct
     */
    protected function setUp()
    {
        $this->media  = new Media();
        $this->object = new RemoteAudioHelper($this->media);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHelper::getMedia
     */
    public function testGetMedia()
    {
        $this->assertEquals(RemoteAudioHandler::CONTENT_TYPE, $this->object->getMedia()->getContentType());
    }
}
