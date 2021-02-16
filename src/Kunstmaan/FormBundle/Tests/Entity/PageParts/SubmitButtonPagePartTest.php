<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Kunstmaan\FormBundle\Entity\PageParts\SubmitButtonPagePart;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use PHPUnit\Framework\TestCase;

class SubmitButtonPagePartTest extends TestCase
{
    /**
     * @var SubmitButtonPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new SubmitButtonPagePart();
    }

    public function testSetGetLabel()
    {
        $object = $this->object;
        $label = 'Test label';
        $object->setLabel($label);
        $this->assertEquals($label, $object->getLabel());
    }

    public function testToString()
    {
        $stringValue = $this->object->__toString();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testGetDefaultView()
    {
        $stringValue = $this->object->getDefaultView();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testGetAdminView()
    {
        $stringValue = $this->object->getAdminView();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals(SubmitButtonPagePartAdminType::class, $this->object->getDefaultAdminType());
    }
}
