<?php

namespace Kunstmaan\FormBundle\Tests\Entity\PageParts;

use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;
use PHPUnit\Framework\TestCase;

class AbstractFormPagePartTest extends TestCase
{
    /**
     * @var AbstractFormPagePart
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = $this->getMockForAbstractClass(AbstractFormPagePart::class);
    }

    public function testGetUniqueId()
    {
        $object = $this->object;
        $this->assertSame(str_replace('\\', '', $object::class), $object->getUniqueId());
    }

    public function testSetGetLabel()
    {
        $object = $this->object;
        $value = 'Some label';
        $object->setLabel($value);
        $this->assertSame($value, $object->getLabel());
    }

    public function testGetAdminView()
    {
        $stringValue = $this->object->getAdminView();
        $this->assertNotNull($stringValue);
        $this->assertIsString($stringValue);
    }
}
