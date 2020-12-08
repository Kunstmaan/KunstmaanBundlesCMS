<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\EnumerationFilterType;
use Symfony\Component\HttpFoundation\Request;

class EnumerationFilterTypeTest extends BaseOrmFilterTest
{
    /**
     * @var EnumerationFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new EnumerationFilterType('enumeration', 'b');
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_enumeration' => 'in', 'filter_value_enumeration' => [1, 2]]);

        $data = [];
        $uniqueId = 'enumeration';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(['comparator' => 'in', 'value' => [1, 2]], $data);
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
        $qb->select('b')
          ->from('Entity', 'b');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'enumeration');

        $this->assertEquals("SELECT b FROM Entity b WHERE b.enumeration $whereClause", $qb->getDQL());
        if ($testValue) {
            $this->assertEquals($value, $qb->getParameter('var_enumeration')->getValue());
        }
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
          ['in', 'IN(:var_enumeration)', [1, 2], true],
          ['notin', 'NOT IN(:var_enumeration)', [1, 2], true],
        ];
    }

    public function testGetTemplate()
    {
        $this->assertEquals('@KunstmaanAdminList/FilterType/enumerationFilter.html.twig', $this->object->getTemplate());
    }
}
