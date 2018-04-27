<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\HeaderPagePart;
use PHPUnit_Framework_TestCase;

/**
 * Class HeaderPagePartTest
 * @package Tests\Kunstmaan\PagePartBundle\Entity
 */
class HeaderPagePartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var HeaderPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new HeaderPagePart();
    }

    public function testLoadValidatorMetadata()
    {
        $metadata = $this->getMockBuilder('Symfony\Component\Validator\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $metadata
            ->expects($this->at(0))
            ->method('addPropertyConstraint')
            ->with('niv', $this->anyThing());

        $metadata
            ->expects($this->at(1))
            ->method('addPropertyConstraint')
            ->with('title', $this->anyThing());

        HeaderPagePart::loadValidatorMetadata($metadata);
    }

    public function testSetGetNiv()
    {
        $this->object->setNiv(5);
        $this->assertEquals(5, $this->object->getNiv());
    }

    public function testSetGetTitle()
    {
        $this->object->setTitle('A test title');
        $this->assertEquals('A test title', $this->object->getTitle());
    }

    public function testToString()
    {
        $this->object->setTitle('A test title');
        $this->assertEquals('HeaderPagePart A test title', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:HeaderPagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
