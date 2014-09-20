<?php
namespace Kunstmaan\MediaPagePartBundle\Tests\Entity;

use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart;
use Kunstmaan\MediaPagePartBundle\Form\ImagePagePartAdminType;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-10-01 at 11:05:56.
 */
class ImagePagePartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ImagePagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ImagePagePart;
    }

    /**
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::getOpenInNewWindow
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::setOpenInNewWindow
     */
    public function testGetSetOpenInNewWindow()
    {
        $this->object->setOpenInNewWindow(true);
        $this->assertEquals(true, $this->object->getOpenInNewWindow());
        $this->object->setOpenInNewWindow(false);
        $this->assertEquals(false, $this->object->getOpenInNewWindow());
    }

    /**
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::setLink
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::getLink
     */
    public function testSetGetLink()
    {
        $this->object->setLink('abc');
        $this->assertEquals('abc', $this->object->getLink());
    }

    /**
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::setMedia
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::getMedia
     */
    public function testSetGetMedia()
    {
        $media = new Media();
        $media->setId(5);
        $this->object->setMedia($media);
        $this->assertEquals(5, $this->object->getMedia()->getId());
    }

    /**
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::setAlttext
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::getAlttext
     */
    public function testSetGetAlttext()
    {
        $this->object->setAltText('bcd');
        $this->assertEquals('bcd', $this->object->getAltText());
    }

    /**
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::getDefaultView
     * @todo   Implement testGetDefaultView().
     */
    public function testGetDefaultView()
    {
        $defaultView = $this->object->getDefaultView();
        $this->assertEquals("KunstmaanMediaPagePartBundle:ImagePagePart:view.html.twig", $defaultView);
    }

    /**
     * @covers Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart::getDefaultAdminType
     * @todo   Implement testGetDefaultAdminType().
     */
    public function testGetDefaultAdminType()
    {
        $defaultAdminType = $this->object->getDefaultAdminType();
        $this->assertTrue($defaultAdminType instanceof ImagePagePartAdminType);
    }
}
