<?php
namespace Kunstmaan\AdminListBundle\Tests\AdminList\Filters\ORM;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

abstract class ORMFilterTestCase extends \PHPUnit_Framework_TestCase
{
    protected function getQueryBuilder()
    {
        // $emMock = $this->getMock('Doctrine\ORM\EntityManager', array(), array(), '', false);
        $config = new \Doctrine\ORM\Configuration();
        $config->setProxyDir(\sys_get_temp_dir());
        $config->setProxyNamespace('KunstmaanTests\Proxy');
        $config->setMetadataDriverImpl(new AnnotationDriver(new IndexedReader(new AnnotationReader())));

        $params = array('driver' => 'pdo_sqlite', 'memory' => true);
        $em =  \Doctrine\ORM\EntityManager::create($params, $config);

        return new QueryBuilder($em);
    }
}
