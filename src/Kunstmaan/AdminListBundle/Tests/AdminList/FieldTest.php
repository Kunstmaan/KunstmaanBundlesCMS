<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    /**
     * @var Field
     */
    protected $object;

    protected function setUp(): void
    {
        $alias = new FieldAlias('ALIAS', 'test');
        $this->object = new Field('name', 'header', true, 'template.html.twig', $alias);
    }

    public function testConstruct()
    {
        $object = new Field('name', 'header', true, 'template.html.twig');
        $this->assertEquals('name', $object->getName());
        $this->assertEquals('header', $object->getHeader());
        $this->assertTrue($object->isSortable());
        $this->assertEquals('template.html.twig', $object->getTemplate());
    }

    public function testConstructorDefaultValues()
    {
        $object = new Field('name', 'header');

        $this->assertEquals('name', $object->getName());
        $this->assertEquals('header', $object->getHeader());
        $this->assertFalse($object->isSortable());
        $this->assertNull($object->getTemplate());
        $this->assertNull($object->getAlias());
    }

    public function testGetName()
    {
        $this->assertEquals('name', $this->object->getName());
    }

    public function testGetHeader()
    {
        $this->assertEquals('header', $this->object->getHeader());
    }

    public function testIsSortable()
    {
        $this->assertTrue($this->object->isSortable());
    }

    public function testGetTemplate()
    {
        $this->assertEquals('template.html.twig', $this->object->getTemplate());
    }

    public function testHasGetAlias()
    {
        $this->assertTrue($this->object->hasAlias());
        $alias = $this->object->getAlias();
        $this->assertInstanceOf(FieldAlias::class, $alias);
        $this->object = new Field('name', 'header', true, 'template.html.twig');
        $this->assertFalse($this->object->hasAlias());
        $this->assertEquals('ALIAS', $alias->getAbbr());
        $this->assertEquals('test', $alias->getRelation());
    }

    public function testGetAliasObject()
    {
        $item = new \stdClass();
        $item->test = 123;
        $val = $this->object->getAliasObj($item);
        $this->assertEquals(123, $val);
    }

    /**
     * @throws \Exception
     */
    public function testGetColumnName()
    {
        $column = $this->object->getColumnName('ALIAS.ABCDEF');
        $this->assertEquals('ABCDEF', $column);
        $this->expectException(\Exception::class);
        $this->object->getColumnName('OMG.CRASH');
    }
}
