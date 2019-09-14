<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\BooleanFilterType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BooleanFilterTypeTest
 */
class BooleanFilterTypeTest extends BaseDbalFilterTest
{
    /**
     * @var BooleanFilterType
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new BooleanFilterType('boolean', 'e');
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
        $this->assertEquals('@KunstmaanAdminList/FilterType/booleanFilter.html.twig', $this->object->getTemplate());
    }
}
