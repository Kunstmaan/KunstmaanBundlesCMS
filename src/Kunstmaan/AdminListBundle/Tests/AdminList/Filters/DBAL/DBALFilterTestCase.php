<?php
namespace Kunstmaan\AdminListBundle\Tests\AdminList\Filters\DBAL;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;

abstract class DBALFilterTestCase extends \PHPUnit_Framework_TestCase
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
