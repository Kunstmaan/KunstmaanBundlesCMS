<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use ArrayIterator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Cache\CacheConfiguration;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineDBALAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filter;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use Pagerfanta\Pagerfanta;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class DBAL extends AbstractDoctrineDBALAdminListConfigurator implements SortableInterface
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

class AbstractDoctrineDBALAdminListConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /** @var DBAL $config */
    private $config;

    /** @var \PHPUnit_Framework_MockObject_MockObject $connection */
    private $connection;

    /**
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $connection = $this->createMock(Connection::class);
        $this->connection = $connection;
        $this->config = new DBAL($connection);

        $mirror = new ReflectionClass(AbstractDoctrineDBALAdminListConfigurator::class);
        $property = $mirror->getProperty('orderBy');
        $property->setAccessible(true);
        $property->setValue($this->config, 'somefield');
        $this->config->setUseDistinctCount(true);

        $filterBuilder = $this->createMock(FilterBuilder::class);
        $filterBuilder->expects($this->any())->method('getCurrentFilters')->willReturn([new Filter('whatever', ['type' => new StringFilterType('whatever')], uniqid())]);
        $this->config->setFilterBuilder($filterBuilder);
    }

    public function testEditUrlFor()
    {
        $config = $this->config;
        $item = ['id' => 666];
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
        $item = ['id' => 666];
        $url = $config->getDeleteUrlFor($item);
        $this->assertCount(2, $url);
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertArrayHasKey('id', $url['params']);
        $this->assertEquals('somebundle_admin_someentity_delete', $url['path']);
        $this->assertCount(1, $url['params']);
        $this->assertEquals(666, $url['params']['id']);
    }

    public function testGetCountField()
    {
        $config = $this->config;
        $config->setCountField('table.column');
        $this->assertEquals('table.column', $config->getCountField());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetPagerFanta()
    {
        $config = $this->config;

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
        $connection->expects($this->any())->method('createQueryBuilder')->willReturn($qb);
        $connection->expects($this->any())->method('getConfiguration')->willReturn($fakeConfig);

        $pager = $config->getPagerfanta();
        $this->assertInstanceOf(Pagerfanta::class, $pager);

        $pager = $this->createMock(Pagerfanta::class);
        $pager->expects($this->once())->method('getNbResults')->willReturn(5);
        $pager->expects($this->once())->method('getCurrentPageResults')->willReturn([1, 2, 3, 4, 5]);

        $mirror = new ReflectionClass(AbstractDoctrineDBALAdminListConfigurator::class);
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
        $queryBuilder = $this->createMock(AbstractQuery::class);
        $queryBuilder->expects($this->once())->method('execute')->willReturn(new ArrayIterator());

        $mirror = new ReflectionClass(AbstractDoctrineDBALAdminListConfigurator::class);
        $property = $mirror->getProperty('queryBuilder');
        $property->setAccessible(true);
        $property->setValue($config, $queryBuilder);

        $it = $config->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $it);
    }
}
