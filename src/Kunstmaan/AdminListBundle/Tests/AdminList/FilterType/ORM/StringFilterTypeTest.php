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
        $qb->select('b')
            ->from('Entity', 'b');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'string');

        $this->assertSame("SELECT b FROM Entity b WHERE b.string $whereClause", $qb->getDQL());
        $this->assertEquals($testValue, $qb->getParameter('var_string')->getValue());
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
