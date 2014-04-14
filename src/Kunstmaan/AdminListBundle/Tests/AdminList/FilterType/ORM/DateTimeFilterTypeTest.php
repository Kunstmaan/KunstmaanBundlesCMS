<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateTimeFilterType;
use Symfony\Component\HttpFoundation\Request;

class DateTimeFilterTypeTest extends ORMFilterTypeTestCase
{
    /**
     * @var DateTimeFilterType
     */
    protected $object;

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return array(
            array('before', '<= :var_datetime', array('date' => '14/04/2014', 'time' => '09:00'), '2014-04-14 09:00'),
            array('after', '> :var_datetime', array('date' => '14/04/2014', 'time' => '10:00'), '2014-04-14 10:00'),
        );
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateTimeFilterType::bindRequest
     */
    public function testBindRequest()
    {
        $request = new Request(array(
            'filter_comparator_datetime' => 'before',
            'filter_value_datetime'      => array('date' => '14/04/2014', 'time' => '09:00')
        ));

        $data     = array();
        $uniqueId = 'datetime';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(
            array('comparator' => 'before', 'value' => array('date' => '14/04/2014', 'time' => '09:00')),
            $data
        );
    }

    /**
     * @param string $comparator  The comparator
     * @param string $whereClause The where clause
     * @param mixed  $value       The value
     * @param mixed  $testValue   The test value
     *
     * @covers       Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateTimeFilterType::apply
     * @dataProvider applyDataProvider
     */
    public function testApply($comparator, $whereClause, $value, $testValue)
    {
        $qb = $this->getQueryBuilder();
        $qb->select('b')
            ->from('Entity', 'b');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(array('comparator' => $comparator, 'value' => $value), 'datetime');

        $this->assertEquals("SELECT b FROM Entity b WHERE b.datetime $whereClause", $qb->getDQL());
        $this->assertEquals($testValue, $qb->getParameter('var_datetime')->getValue());
    }

    /**
     * @covers Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateTimeFilterType::getTemplate
     */
    public function testGetTemplate()
    {
        $this->assertEquals(
            'KunstmaanAdminListBundle:FilterType:dateTimeFilter.html.twig',
            $this->object->getTemplate()
        );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new DateTimeFilterType('datetime', 'b');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}
