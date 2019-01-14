<?php
namespace Kunstmaan\AdminListBundle\Tests\Helper;

use Codeception\Stub;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\Expression\ExpressionBuilder;
use Doctrine\ORM\EntityManager;

class Unit extends \Codeception\Module
{
    public function getDBALQueryBuilder()
    {
        /* @var Stub $conn */
        $conn = Stub::make(Connection::class, [
        ]);

        $expressionBuilder = new ExpressionBuilder($conn);
        /* @var Stub $conn */
        $conn = Stub::make(Connection::class, [
            'getExpressionBuilder' => $expressionBuilder
        ]);

        return new \Doctrine\DBAL\Query\QueryBuilder($conn);
    }

    public function getORMQueryBuilder()
    {
        $em = Stub::make(EntityManager::class, []);

        return new \Doctrine\ORM\QueryBuilder($em);
    }
}
