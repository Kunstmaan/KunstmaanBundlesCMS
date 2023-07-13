<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\EnumerationFilterType;
use Symfony\Component\HttpFoundation\Request;

class EnumerationFilterTypeTest extends BaseDbalFilterTest
{
    /**
     * @var EnumerationFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new EnumerationFilterType('enumeration', 'e');
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_enumeration' => 'in', 'filter_value_enumeration' => [1, 2]]);

        $data = [];
        $uniqueId = 'enumeration';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertSame(['comparator' => 'in', 'value' => [1, 2]], $data);
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
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'enumeration');

        $this->assertSame("SELECT * FROM entity e WHERE e.enumeration $whereClause", $qb->getSQL());
        if ($testValue) {
            $this->assertEquals($value, $qb->getParameter('var_enumeration'));
        }
    }

    public static function applyDataProvider(): \Iterator
    {
        yield ['in', 'IN (:var_enumeration)', [1, 2], true];
        yield ['notin', 'NOT IN (:var_enumeration)', [1, 2], true];
    }

    public function testGetComparator()
    {
        $request = new Request(['filter_comparator_enumeration' => 'in', 'filter_value_enumeration' => [1, 2]]);
        $data = [];
        $uniqueId = 'enumeration';
        $this->object->bindRequest($request, $data, $uniqueId);
        $this->assertSame('in', $this->object->getComparator());
    }

    public function testGetValue()
    {
        $request = new Request(['filter_comparator_enumeration' => 'in', 'filter_value_enumeration' => [1, 2]]);
        $data = [];
        $uniqueId = 'enumeration';
        $this->object->bindRequest($request, $data, $uniqueId);
        $this->assertEquals($this->object->getValue(), [1, 2]);
    }

    public function testGetTemplate()
    {
        $this->assertSame('@KunstmaanAdminList/FilterType/enumerationFilter.html.twig', $this->object->getTemplate());
    }
}
