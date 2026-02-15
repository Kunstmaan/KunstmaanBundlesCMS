<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\HeaderPagePart;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class HeaderPagePartTest extends TestCase
{
    /**
     * @var HeaderPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new HeaderPagePart();
    }

    public function testLoadValidatorMetadata()
    {
        $metadata = new ClassMetadata(HeaderPagePart::class);

        HeaderPagePart::loadValidatorMetadata($metadata);

        $this->assertTrue($metadata->hasPropertyMetadata('niv'));
        $this->assertInstanceOf(NotBlank::class, $metadata->getPropertyMetadata('niv')[0]->getConstraints()[0]);
        $this->assertTrue($metadata->hasPropertyMetadata('title'));
        $this->assertInstanceOf(NotBlank::class, $metadata->getPropertyMetadata('title')[0]->getConstraints()[0]);
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
        $this->assertEquals('@KunstmaanPagePart/HeaderPagePart/view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
