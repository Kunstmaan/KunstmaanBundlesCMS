<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\Tests\unit\AdminList\FilterType\ORM\BaseOrmFilterTest;
use Kunstmaan\AdminListBundle\Tests\UnitTester;
use Symfony\Component\HttpFoundation\Request;

class BooleanFilterTypeTest extends BaseOrmFilterTest
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @var BooleanFilterType
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new BooleanFilterType('boolean', 'b');
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
        $qb = $this->getQueryBuilder();
        $qb->select('b')
            ->from('Entity', 'b');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(array('value' => $value), 'boolean');

        $this->assertEquals("SELECT b FROM Entity b WHERE b.boolean = $value", $qb->getDQL());
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
