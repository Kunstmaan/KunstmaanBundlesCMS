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

    public function testBindRequestFromQuery()
    {
        $queryData = [
            'filter_columnname' => [
                'column1',
                'column2',
            ],
            'filter_uniquefilterid' => [
                '1',
                '2',
            ],
            'filter_value_1' => 'value_1',
            'filter_value_2' => 'value_2',
            'filter_comparator_2' => 'equals',
        ];

        $request = new Request($queryData);
        $request->setSession($this->createMock(Session::class));

        $filter1 = new StringFilterType('col1', 'e');
        $filter2 = new StringFilterType('col2', 'e');
        $filterDef = [
            'column1' => ['type' => $filter1, 'options' => ['option1' => 'value1'], 'filtername' => 'filter1Name'],
            'column2' => ['type' => $filter2, 'options' => ['option1' => 'value1'], 'filtername' => 'filter2Name'],
        ];
        $this->object->add('column1', $filter1, 'filter1Name', ['option1' => 'value1']);
        $this->object->add('column2', $filter2, 'filter2Name', ['option1' => 'value1']);

        $this->object->bindRequest($request);

        $this->assertEquals($filterDef, $this->object->getFilterDefinitions());
        $this->assertCount(2, $this->object->getCurrentFilters());
    }
}
