<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Filter;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class FilterTest extends TestCase
{
    /**
     * @var Filter
     */
    protected $object;

    protected function setUp(): void
    {
        $type = new StringFilterType('string', 'b');
        $filterDef = ['type' => $type, 'options' => ['x' => 'y'], 'filtername' => 'filterName'];
        $this->object = new Filter('columnName', $filterDef, 'string');
    }

    public function testConstruct()
    {
        $filterDef = ['type' => new StringFilterType('string', 'b'), 'options' => [], 'filtername' => 'filterName'];
        $object = new Filter('columnName', $filterDef, 'string');

        $this->assertEquals('columnName', $object->getColumnName());
        $this->assertEquals('string', $object->getUniqueId());
        $this->assertInstanceOf('Kunstmaan\AdminListBundle\AdminList\FilterType\FilterTypeInterface', $object->getType());
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_string' => 'equals', 'filter_value_string' => 'TheStringValue']);
        $this->object->bindRequest($request);

        $this->assertEquals(['comparator' => 'equals', 'value' => 'TheStringValue'], $this->object->getData());
    }

    public function testGetOptions()
    {
        $options = $this->object->getOptions();
        $this->assertTrue(\is_array($options));
        $this->assertArrayHasKey('x', $options);
    }

    public function testApply()
    {
        $type = $this->createMock(StringFilterType::class);
        $type->expects($this->once())->method('apply');
        $filterDef = ['type' => $type, 'options' => ['x' => 'y'], 'filtername' => 'filterName'];
        $this->object = new Filter('columnName', $filterDef, 'string');
        $this->object->apply();
    }
}
