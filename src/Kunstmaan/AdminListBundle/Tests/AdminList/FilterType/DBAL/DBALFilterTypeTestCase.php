<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Connection;

/**
 * DBALFilterTypeTestCase
 */
abstract class DBALFilterTypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getQueryBuilder()
    {
        /* @var Connection $conn */
        $conn = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);

        $expressionBuilder = new ExpressionBuilder($conn);

        $conn->expects($this->any())
            ->method('getExpressionBuilder')
            ->will($this->returnValue($expressionBuilder));

        return new QueryBuilder($conn);
    }
}
