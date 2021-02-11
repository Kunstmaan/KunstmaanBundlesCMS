<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class FilterBuilderTest extends TestCase
{
    /**
     * @var FilterBuilder
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new FilterBuilder();
    }

    public function testAdd()
    {
        $result = $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', ['option1' => 'value1']);

        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\FilterBuilder', $result);
    }

    public function testGet()
    {
        $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', ['option1' => 'value1']);
        $definition = $this->object->get('columnName');

        $this->assertArrayHasKey('type', $definition);
        $this->assertArrayHasKey('filtername', $definition);
        $this->assertArrayHasKey('options', $definition);
        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType', $definition['type']);
        $this->assertEquals('filterName', $definition['filtername']);
        $this->assertEquals(['option1' => 'value1'], $definition['options']);
        $this->assertTrue(\is_array($this->object->getCurrentParameters()));
        $this->assertTrue(\is_array($this->object->getCurrentFilters()));
    }

    public function testRemove()
    {
        $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', ['option1' => 'value1']);
        $definition = $this->object->get('columnName');
        $this->assertNotNull($definition);

        $this->assertInstanceOf(FilterBuilder::class, $this->object->remove('columnName'));
        $definition = $this->object->get('columnName');
        $this->assertNull($definition);
    }

    public function testHas()
    {
        $this->assertFalse($this->object->has('columnName'));
        $this->object->add('columnName', new StringFilterType('string', 'e'), 'filterName', ['option1' => 'value1']);
        $this->assertTrue($this->object->has('columnName'));
    }

    public function testGetFilterDefinitions()
    {
        $filter = new StringFilterType('string', 'e');
        $filterDef = ['columnName' => ['type' => $filter, 'options' => ['option1' => 'value1'], 'filtername' => 'filterName']];
        $this->object->add('columnName', $filter, 'filterName', ['option1' => 'value1']);

        $this->assertEquals($filterDef, $this->object->getFilterDefinitions());
    }

    public function testBindRequest()
    {
        $session = $this->createMock(Session::class);
        $session->expects($this->any())->method('has')->with('filter_')->willReturn(false);
        $session->expects($this->any())->method('has')->willReturn(true);
        $session->expects($this->any())->method('get')->willReturn(['filter_columnname' => ['something' => 'columnName']]);
        $request = new Request();
        $request->setSession($session);

        $filter = new StringFilterType('string', 'e');
        $filterDef = ['columnName' => ['type' => $filter, 'options' => ['option1' => 'value1'], 'filtername' => 'filterName']];
        $this->object->add('columnName', $filter, 'filterName', ['option1' => 'value1']);

        $this->object->bindRequest($request);

        $this->assertEquals($filterDef, $this->object->getFilterDefinitions());
    }
}
