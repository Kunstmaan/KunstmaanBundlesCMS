<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Symfony\Component\HttpFoundation\Request;

class StringFilterTypeTest extends BaseOrmFilterTest
{
    /**
     * @var StringFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new StringFilterType('string', 'b');
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_string' => 'equals', 'filter_value_string' => 'TheStringValue']);

        $data = [];
        $uniqueId = 'string';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(['comparator' => 'equals', 'value' => 'TheStringValue'], $data);
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
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'string');

        $this->assertEquals("SELECT b FROM Entity b WHERE b.string $whereClause", $qb->getDQL());
        $this->assertEquals($testValue, $qb->getParameter('var_string')->getValue());
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['equals', '= :var_string', 'AStringValue1', 'AStringValue1'],
            ['notequals', '<> :var_string', 'AStringValue2', 'AStringValue2'],
            ['contains', 'LIKE :var_string', 'AStringValue3', '%AStringValue3%'],
            ['doesnotcontain', 'NOT LIKE :var_string', 'AStringValue4', '%AStringValue4%'],
            ['startswith', 'LIKE :var_string', 'AStringValue5', 'AStringValue5%'],
            ['endswith', 'LIKE :var_string', 'AStringValue6', '%AStringValue6'],
        ];
    }

    public function testGetTemplate()
    {
        $this->assertEquals('@KunstmaanAdminList/FilterType/stringFilter.html.twig', $this->object->getTemplate());
    }
}
