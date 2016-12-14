<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\FilterType\ORM;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\QueryBuilder;

/**
 * ORMFilterTypeTestCase
 */
abstract class ORMFilterTypeTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getQueryBuilder()
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setProxyNamespace('KunstmaanTests\Proxy');
        $config->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader()));

        $params = array('driver' => 'pdo_sqlite', 'memory' => true);
        $em =  \Doctrine\ORM\EntityManager::create($params, $config);

        return new QueryBuilder($em);
    }
}
