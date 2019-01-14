<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\HeaderPagePart;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class HeaderPagePartTest
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
        $metadata = new ClassMetadata(HeaderPagePart::class);

        HeaderPagePart::loadValidatorMetadata($metadata);
        $this->assertArrayHasKey('niv', $metadata->properties);
        $this->assertInstanceOf(NotBlank::class, $metadata->properties['niv']->getConstraints()[0]);
        $this->assertArrayHasKey('title', $metadata->properties);
        $this->assertInstanceOf(NotBlank::class, $metadata->properties['title']->getConstraints()[0]);
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
