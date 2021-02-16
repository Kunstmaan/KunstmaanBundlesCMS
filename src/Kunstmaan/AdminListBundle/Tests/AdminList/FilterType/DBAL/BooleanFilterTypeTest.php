<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\BooleanFilterType;
use Symfony\Component\HttpFoundation\Request;

class BooleanFilterTypeTest extends BaseDbalFilterTest
{
    /**
     * @var BooleanFilterType
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new BooleanFilterType('boolean', 'e');
    }

    public function testBindRequest()
    {
        $request = new Request(['filter_value_boolean' => 'true']);

        $data = [];
        $uniqueId = 'boolean';
        $this->object->bindRequest($request, $data, $uniqueId);

        $this->assertEquals(['value' => 'true'], $data);
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
        $this->object->apply(['value' => $value], 'boolean');

        $this->assertEquals("SELECT * FROM entity e WHERE e.boolean = $value", $qb->getSQL());
    }

    /**
     * @return array
     */
    public static function applyDataProvider()
    {
        return [
            ['true'],
            ['false'],
        ];
    }

    public function testGetTemplate()
    {
        $this->assertEquals('@KunstmaanAdminList/FilterType/booleanFilter.html.twig', $this->object->getTemplate());
    }
}
