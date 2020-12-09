<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\NumberFilterType;
use Symfony\Component\HttpFoundation\Request;

class NumberFilterTypeTest extends BaseDbalFilterTest
{
    /**
     * @var NumberFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new NumberFilterType('number', 'e');
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_number' => 'eq', 'filter_value_number' => 1]);

        $data = [];
        $uniqueId = 'number';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(['comparator' => 'eq', 'value' => 1], $data);
    }

    /**
     * @param string $comparator  The comparator
     * @param string $whereClause The where clause
     * @param mixed  $value       The value
     * @param mixed  $testValue   The test value
     *
     * @dataProvider applyDataProvider
     */
    public function testApply($comparator, $whereClause, $value, $testValue)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('*')
            ->from('entity', 'e');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'number');

        $this->assertEquals("SELECT * FROM entity e WHERE e.number $whereClause", $qb->getSQL());
        if ($testValue) {
            $this->assertEquals($value, $qb->getParameter('var_number'));
        }
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['eq', '= :var_number', 1, true],
            ['neq', '<> :var_number', 2, true],
            ['lt', '< :var_number', 3, true],
            ['lte', '<= :var_number', 4, true],
            ['gt', '> :var_number', 5, true],
            ['gte', '>= :var_number', 6, true],
            ['isnull', 'IS NULL', 0, false],
            ['isnotnull', 'IS NOT NULL', 0, false],
        ];
    }

    public function testGetTemplate()
    {
        $this->assertEquals('@KunstmaanAdminList/FilterType/numberFilter.html.twig', $this->object->getTemplate());
    }
}
