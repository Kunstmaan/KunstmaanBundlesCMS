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

        $this->assertSame(['comparator' => 'eq', 'value' => 1], $data);
    }

    /**
     * @param string $comparator  The comparator
     * @param string $whereClause The where clause
     * @param mixed  $value       The value
     * @param mixed  $testValue   The test value
     *
     * @dataProvider applyDataProvider
     */
    public function testApply($comparator, $whereClause, mixed $value, mixed $testValue)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('*')
            ->from('entity', 'e');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'number');

        $this->assertSame("SELECT * FROM entity e WHERE e.number $whereClause", $qb->getSQL());
        if ($testValue) {
            $this->assertEquals($value, $qb->getParameter('var_number'));
        }
    }

    public static function applyDataProvider(): \Iterator
    {
        yield ['eq', '= :var_number', 1, true];
        yield ['neq', '<> :var_number', 2, true];
        yield ['lt', '< :var_number', 3, true];
        yield ['lte', '<= :var_number', 4, true];
        yield ['gt', '> :var_number', 5, true];
        yield ['gte', '>= :var_number', 6, true];
        yield ['isnull', 'IS NULL', 0, false];
        yield ['isnotnull', 'IS NOT NULL', 0, false];
    }

    public function testGetTemplate()
    {
        $this->assertSame('@KunstmaanAdminList/FilterType/numberFilter.html.twig', $this->object->getTemplate());
    }
}
