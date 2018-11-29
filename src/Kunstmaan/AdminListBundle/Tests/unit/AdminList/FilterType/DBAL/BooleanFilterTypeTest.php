<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Codeception\Test\Unit;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\BooleanFilterType;
use Kunstmaan\AdminListBundle\Tests\UnitTester;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BooleanFilterTypeTest
 */
class BooleanFilterTypeTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var BooleanFilterType
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function _before()
    {
        $this->object = new BooleanFilterType('boolean', 'e');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testBindRequest()
    {
        $request = new Request(array('filter_value_boolean' => 'true'));

        $data = array();
        $uniqueId = 'boolean';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(array('value' => 'true'), $data);
    }

    /**
     * @param mixed $value
     *
     * @dataProvider applyDataProvider
     */
    public function testApply($value)
    {
        $qb = $this->tester->getDBALQueryBuilder();
        $qb->select('*')
           ->from('entity', 'e');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(array('value' => $value), 'boolean');

        $this->assertEquals("SELECT * FROM entity e WHERE e.boolean = $value", $qb->getSQL());
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return array(
            array('true'),
            array('false'),
        );
    }

    public function testGetTemplate()
    {
        $this->assertEquals('KunstmaanAdminListBundle:FilterType:booleanFilter.html.twig', $this->object->getTemplate());
    }
}
