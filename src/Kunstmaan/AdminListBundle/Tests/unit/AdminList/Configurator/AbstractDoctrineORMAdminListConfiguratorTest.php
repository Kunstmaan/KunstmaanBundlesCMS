<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use ArrayIterator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filter;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Kunstmaan\LeadGenerationBundle\Tests\unit\Entity\Popup\Popup;

class ORM extends AbstractDoctrineORMAdminListConfigurator implements SortableInterface
{
    /**
     * @return mixed
     */
    public function getBundleName()
    {
        return 'SomeBundle';
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return 'SomeEntity';
    }

    /**
     * @return mixed
     */
    public function buildFields()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getSortableField()
    {
        return 'sortfield';
    }
}

class AbstractDoctrineORMAdminListConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /** @var ORM $config */
    private $config;

    /** @var \PHPUnit_Framework_MockObject_MockObject $em */
    private $em;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $em = $this->createMock(EntityManager::class);
        $this->em = $em;
        $this->config = new ORM($em);

        $mirror = new ReflectionClass(AbstractDoctrineORMAdminListConfigurator::class);
        $property = $mirror->getProperty('orderBy');
        $property->setAccessible(true);
        $property->setValue($this->config, 'somefield');
    }

    public function testGetSetEntityManager()
    {
        $config = $this->config;
        $em = $this->createMock(EntityManager::class);
        $config->setEntityManager($em);
        $this->assertInstanceOf(EntityManager::class, $config->getEntityManager());
    }

    public function testGetSetPermissionDefinition()
    {
        $config = $this->config;
        $this->assertNull($config->getPermissionDefinition());
        $config->setPermissionDefinition(new PermissionDefinition(['something']));
        $this->assertInstanceOf(PermissionDefinition::class, $config->getPermissionDefinition());
    }

    public function testGetQuery()
    {
        $config = $this->config;
        $em = $this->em;
        $filterBuilder = $this->createMock(FilterBuilder::class);
        $filterBuilder->expects($this->once())->method('getCurrentFilters')->willReturn([new Filter('whatever', ['type' => new StringFilterType('whatever')], uniqid())]);
        $config->setFilterBuilder($filterBuilder);
        $cacheConfig = $this->createMock(CacheConfiguration::class);
        $cacheConfig->expects($this->any())->method('getCacheLogger')->willReturn('whatever');
        $fakeConfig = $this->createMock(Configuration::class);
        $fakeConfig->expects($this->any())->method('getDefaultQueryHints')->willReturn($fakeConfig);
        $fakeConfig->expects($this->any())->method('isSecondLevelCacheEnabled')->willReturn($fakeConfig);
        $fakeConfig->expects($this->any())->method('getSecondLevelCacheConfiguration')->willReturn($cacheConfig);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->any())->method('setParameters')->willReturn($qb);
        $qb->expects($this->any())->method('setMaxResults')->willReturn($qb);
        $em->expects($this->any())->method('createQuery')->willReturn($qb);
        $em->expects($this->any())->method('getRepository')->willReturn($em);
        $em->expects($this->any())->method('createQueryBuilder')->willReturn($qb);
        $em->expects($this->any())->method('getConfiguration')->willReturn($fakeConfig);
        $query = new Query($em);
        $qb->expects($this->any())->method('getQuery')->willReturn($query);
        $query = $config->getQuery();
        $this->assertInstanceOf(Query::class, $query);
    }

    public function testEditUrlFor()
    {
        $config = $this->config;
        $item = new Popup();
        $item->setId(666);
        $url = $config->getEditUrlFor($item);
        $this->assertCount(2, $url);
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertArrayHasKey('id', $url['params']);
        $this->assertEquals('somebundle_admin_someentity_edit', $url['path']);
        $this->assertCount(1, $url['params']);
        $this->assertEquals(666, $url['params']['id']);
    }

    public function testDeleteUrlFor()
    {
        $config = $this->config;
        $item = new Popup();
        $item->setId(666);
        $url = $config->getDeleteUrlFor($item);
        $this->assertCount(2, $url);
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertArrayHasKey('id', $url['params']);
        $this->assertEquals('somebundle_admin_someentity_delete', $url['path']);
        $this->assertCount(1, $url['params']);
        $this->assertEquals(666, $url['params']['id']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetPagerFanta()
    {
        $config = $this->config;
        $em = $this->em;

        $cacheConfig = $this->createMock(CacheConfiguration::class);
        $cacheConfig->expects($this->any())->method('getCacheLogger')->willReturn('whatever');

        $fakeConfig = $this->createMock(Configuration::class);
        $fakeConfig->expects($this->any())->method('getDefaultQueryHints')->willReturn(['doctrine_paginator.distinct' => 'blah']);
        $fakeConfig->expects($this->any())->method('isSecondLevelCacheEnabled')->willReturn($fakeConfig);
        $fakeConfig->expects($this->any())->method('getSecondLevelCacheConfiguration')->willReturn($cacheConfig);

        $platform = $this->createMock(MySQL57Platform::class);
        $platform->expects($this->any())->method('getSQLResultCasing')->willReturn('string');

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->any())->method('getDatabasePlatform')->willReturn($platform);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->any())->method('setParameters')->willReturn($qb);
        $qb->expects($this->any())->method('setMaxResults')->willReturn($qb);
        $em->expects($this->any())->method('createQuery')->willReturn($qb);
        $em->expects($this->any())->method('getRepository')->willReturn($em);
        $em->expects($this->any())->method('createQueryBuilder')->willReturn($qb);
        $em->expects($this->any())->method('getConfiguration')->willReturn($fakeConfig);
        $em->expects($this->any())->method('getConnection')->willReturn($connection);
        $qb->expects($this->any())->method('getQuery')->willReturn(new Query($em));

        $pager = $config->getPagerfanta();
        $this->assertInstanceOf(Pagerfanta::class, $pager);

        $pager = $this->createMock(Pagerfanta::class);
        $pager->expects($this->once())->method('getNbResults')->willReturn(5);
        $pager->expects($this->once())->method('getCurrentPageResults')->willReturn([1, 2, 3, 4, 5]);

        $mirror = new ReflectionClass(AbstractDoctrineORMAdminListConfigurator::class);
        $property = $mirror->getProperty('pagerfanta');
        $property->setAccessible(true);
        $property->setValue($config, $pager);

        $count = $config->getCount();
        $this->assertEquals(5, $count);

        $items = $config->getItems();
        $this->assertCount(5, $items);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetIterator()
    {
        $config = $this->config;
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())->method('iterate')->willReturn(new ArrayIterator());

        $mirror = new ReflectionClass(AbstractDoctrineORMAdminListConfigurator::class);
        $property = $mirror->getProperty('query');
        $property->setAccessible(true);
        $property->setValue($config, $query);

        $it = $config->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $it);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetQueryWithAclEnabled()
    {
        $config = $this->config;
        $em = $this->em;

        $cacheConfig = $this->createMock(CacheConfiguration::class);
        $cacheConfig->expects($this->any())->method('getCacheLogger')->willReturn('whatever');
        $fakeConfig = $this->createMock(Configuration::class);
        $fakeConfig->expects($this->any())->method('getDefaultQueryHints')->willReturn($fakeConfig);
        $fakeConfig->expects($this->any())->method('isSecondLevelCacheEnabled')->willReturn($fakeConfig);
        $fakeConfig->expects($this->any())->method('getSecondLevelCacheConfiguration')->willReturn($cacheConfig);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->any())->method('setParameters')->willReturn($qb);
        $qb->expects($this->any())->method('setMaxResults')->willReturn($qb);
        $em->expects($this->any())->method('createQuery')->willReturn($qb);
        $em->expects($this->any())->method('getRepository')->willReturn($em);
        $em->expects($this->any())->method('createQueryBuilder')->willReturn($qb);
        $em->expects($this->any())->method('getConfiguration')->willReturn($fakeConfig);

        $query = new Query($em);
        $aclHelper = $this->createMock(AclHelper::class);
        $aclHelper->expects($this->once())->method('apply')->willReturn($query);
        $mirror = new ReflectionClass(AbstractDoctrineORMAdminListConfigurator::class);
        $property = $mirror->getProperty('aclHelper');
        $property->setAccessible(true);
        $property->setValue($config, $aclHelper);
        $property = $mirror->getProperty('permissionDef');
        $property->setAccessible(true);
        $property->setValue($config, new PermissionDefinition(['admin']));

        $qb->expects($this->any())->method('getQuery')->willReturn($query);
        $query = $config->getQuery();
        $this->assertInstanceOf(Query::class, $query);
    }
}
