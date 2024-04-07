<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\DBAL;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use PHPUnit\Framework\TestCase;

abstract class BaseDbalFilterTest extends TestCase
{
    public function getQueryBuilder()
    {
        $options = ['driver' => 'pdo_sqlite', 'path' => 'database.sqlite'];
        $config = new Configuration();
        $config->setSchemaManagerFactory(new DefaultSchemaManagerFactory());
        $conn = DriverManager::getConnection($options, $config);

        return new QueryBuilder($conn);
    }
}
