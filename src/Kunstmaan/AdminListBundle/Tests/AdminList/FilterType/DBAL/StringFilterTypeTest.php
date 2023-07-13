<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;
use Symfony\Component\HttpFoundation\Request;

class StringFilterTypeTest extends BaseDbalFilterTest
{
    /**
     * @var StringFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new StringFilterType('string', 'e');
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_comparator_string' => 'equals', 'filter_value_string' => 'TheStringValue']);

        $data = [];
        $uniqueId = 'string';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertSame(['comparator' => 'equals', 'value' => 'TheStringValue'], $data);
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
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'string');

        $this->assertSame("SELECT * FROM entity e WHERE e.string $whereClause", $qb->getSQL());
        $this->assertEquals($testValue, $qb->getParameter('var_string'));
    }

    public static function applyDataProvider(): \Iterator
    {
        yield ['equals', '= :var_string', 'AStringValue1', 'AStringValue1'];
        yield ['notequals', '<> :var_string', 'AStringValue2', 'AStringValue2'];
        yield ['contains', 'LIKE :var_string', 'AStringValue3', '%AStringValue3%'];
        yield ['doesnotcontain', 'NOT LIKE :var_string', 'AStringValue4', '%AStringValue4%'];
        yield ['startswith', 'LIKE :var_string', 'AStringValue5', 'AStringValue5%'];
        yield ['endswith', 'LIKE :var_string', 'AStringValue6', '%AStringValue6'];
    }

    public function testGetTemplate()
    {
        $this->assertSame('@KunstmaanAdminList/FilterType/stringFilter.html.twig', $this->object->getTemplate());
    }
}
