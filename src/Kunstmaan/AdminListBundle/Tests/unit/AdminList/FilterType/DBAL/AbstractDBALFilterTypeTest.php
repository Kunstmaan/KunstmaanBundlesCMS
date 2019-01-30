<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\AbstractDBALFilterType;
use PHPUnit\Framework\TestCase;

class AbstractDBALFilterTypeTest extends TestCase
{
    public function testSetQueryBuilder()
    {
        /* @var AbstractDBALFilterType $object */
        $object = $this->getMockForAbstractClass('Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\AbstractDBALFilterType', array('column', 'alias'));

        /* @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getMockBuilder('Doctrine\DBAL\Query\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $object->setQueryBuilder($queryBuilder);
    }
}
