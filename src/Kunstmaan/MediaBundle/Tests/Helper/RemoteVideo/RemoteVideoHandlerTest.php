<?php


namespace Kunstmaan\MediaBundle\Tests\Helper\RemoteVideo;


use Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHandler;

class RemoteVideoHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param $data
     * @param $expected
     *
     * @dataProvider provider
     */
    public function testYoutubeUrl($url, $type, $code)
    {
        $handler = new RemoteVideoHandler(0);

        $result = $handler->createNew($url);

        $this->assertInstanceOf('Kunstmaan\MediaBundle\Entity\Media', $result);
        $this->assertEquals($type, $result->getMetadataValue('type'));
        $this->assertEquals($code, $result->getMetadataValue('code'));
    }

    public function provider()
    {
        return array(
            array('https://youtu.be/jPDHAXV8E6w', 'youtube', 'jPDHAXV8E6w'),
            array('https://www.youtube.com/watch?v=jPDHAXV8E6w', 'youtube', 'jPDHAXV8E6w'),
        );
    }
}
