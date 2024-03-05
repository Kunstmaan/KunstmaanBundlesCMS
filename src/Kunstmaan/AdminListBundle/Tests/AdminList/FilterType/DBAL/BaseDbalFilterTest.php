<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;

abstract class BaseDbalFilterTest extends TestCase
{
    public function getQueryBuilder()
    {
        $conn = $this->createMock(Connection::class);
        $expressionBuilder = new ExpressionBuilder($conn);

        $conn->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        if (method_exists(Connection::class, 'createExpressionBuilder')) {
            $conn->method('createExpressionBuilder')->willReturn($expressionBuilder);
        }
        $conn->method('getExpressionBuilder')->willReturn($expressionBuilder);

        return new QueryBuilder($conn);
    }
}
