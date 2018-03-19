<?php

namespace Tests\Kunstmaan\AdminListBundle\AdminList\Configurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Pagerfanta\Pagerfanta;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Tests\Kunstmaan\LeadGenerationBundle\Entity\Popup\Popup;

class ORM extends AbstractDoctrineORMAdminListConfigurator
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

}

class AbstractDoctrineORMAdminListConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /** @var ORM $config */
    private $config;

    /** @var \PHPUnit_Framework_MockObject_MockObject $em */
    private $em;


    public function setUp()
    {
        $em = $this->createMock(EntityManager::class);
        $this->em = $em;
        $this->config = new ORM($em);
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
        $qb->expects($this->any())->method('getQuery')->willReturn(new Query($em));
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
        $pager->expects($this->once())->method('getCurrentPageResults')->willReturn([1,2,3,4,5]);

        $mirror = new ReflectionClass(AbstractDoctrineORMAdminListConfigurator::class);
        $property = $mirror->getProperty('pagerfanta');
        $property->setAccessible(true);
        $property->setValue($config, $pager);

        $count = $config->getCount();
        $this->assertEquals(5, $count);

        $items = $config->getItems();
        $this->assertCount(5, $items);
    }
}