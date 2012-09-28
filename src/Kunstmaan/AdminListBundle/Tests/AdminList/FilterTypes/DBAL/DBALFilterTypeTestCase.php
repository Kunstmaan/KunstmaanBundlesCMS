<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterTypes\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

/**
 * DBALFilterTypeTestCase
 */
abstract class DBALFilterTypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getQueryBuilder()
    {
        $conn = $this->getMock('Doctrine\DBAL\Connection', array(), array(), '', false);

        $expressionBuilder = new ExpressionBuilder($conn);

        $conn->expects($this->any())
            ->method('getExpressionBuilder')
            ->will($this->returnValue($expressionBuilder));

        return new QueryBuilder($conn);
    }
}
