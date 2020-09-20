<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateTimeFilterType;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

class DateTimeFilterTypeTest extends BaseOrmFilterTest
{
    /**
     * @var DateTimeFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new DateTimeFilterType('datetime', 'b');
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['before', '<= :var_datetime', ['date' => '14/04/2014', 'time' => '09:00'], '2014-04-14 09:00'],
            ['after', '> :var_datetime', ['date' => '14/04/2014', 'time' => '10:00'], '2014-04-14 10:00'],
        ];
    }

    public function testBindRequest()
    {
        $request = new Request([
            'filter_comparator_datetime' => 'before',
            'filter_value_datetime' => ['date' => '14/04/2014', 'time' => '09:00'],
        ]);

        $data = [];
        $uniqueId = 'datetime';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(
            ['comparator' => 'before', 'value' => ['date' => '14/04/2014', 'time' => '09:00']],
            $data
        );
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
        $this->object->apply(['comparator' => $comparator, 'value' => $value], 'datetime');

        $this->assertEquals("SELECT b FROM Entity b WHERE b.datetime $whereClause", $qb->getDQL());
        $this->assertEquals($testValue, $qb->getParameter('var_datetime')->getValue());
    }

    public function testGetTemplate()
    {
        $this->assertEquals(
            '@KunstmaanAdminList/FilterType/dateTimeFilter.html.twig',
            $this->object->getTemplate()
        );
    }

    /**
     * @throws \ReflectionException
     */
    public function testApplyReturnsNull()
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects($this->never())->method('setParameter');
        $mirror = new ReflectionClass(DateTimeFilterType::class);
        $property = $mirror->getProperty('queryBuilder');
        $property->setAccessible(true);
        $property->setValue($this->object, $queryBuilder);

        $badData = [
            'value' => [
               'date' => 'oopsNotADate',
               'time' => 'oopsNotATime',
            ],
            'comparator' => 'true',
        ];
        $this->object->apply($badData, uniqid());
    }
}
