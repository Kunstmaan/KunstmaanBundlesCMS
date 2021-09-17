<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
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

    protected function setUp(): void
    {
        $this->object = new BooleanFilterType('boolean', 'b');
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
        $qb->select('b')
            ->from('Entity', 'b');
        $this->object->setQueryBuilder($qb);
        $this->object->apply(['value' => $value], 'boolean');

        $this->assertEquals("SELECT b FROM Entity b WHERE b.boolean = $value", $qb->getDQL());
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
