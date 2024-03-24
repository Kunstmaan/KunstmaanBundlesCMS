<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineDBALAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filter;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\DBAL\AbstractDBALFilterType;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AbstractDoctrineDBALAdminListConfiguratorTest extends TestCase
{
    /** @var Connection */
    private $connectionMock;

    public function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);
        $this->connectionMock->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $this->connectionMock
            ->expects($this->any())
            ->method('executeQuery')
            ->willReturn(interface_exists(DriverStatement::class) ? $this->createMock(Statement::class) : $this->createMock(Result::class))
        ;
    }

    public function testGetEditUrl()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator();

        $editUrl = $adminlistConfigurator->getEditUrlFor(['id' => 888]);
        $this->assertIsArray($editUrl);
        $this->assertArrayHasKey('path', $editUrl);
        $this->assertArrayHasKey('params', $editUrl);
        $this->assertEquals('app_admin_user_' . AbstractDoctrineDBALadminListConfigurator::SUFFIX_EDIT, $editUrl['path']);
        $this->assertContains(888, $editUrl['params']);
    }

    public function testGetDeleteUrl()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator();

        $editUrl = $adminlistConfigurator->getDeleteUrlFor(['id' => 888]);
        $this->assertIsArray($editUrl);
        $this->assertArrayHasKey('path', $editUrl);
        $this->assertArrayHasKey('params', $editUrl);
        $this->assertEquals('app_admin_user_' . AbstractDoctrineDBALAdminListConfigurator::SUFFIX_DELETE, $editUrl['path']);
        $this->assertContains(888, $editUrl['params']);
    }

    public function testGetPagerFanta()
    {
        $this->assertInstanceOf(Pagerfanta::class, $this->setUpAdminlistConfigurator()->getPagerfanta());
    }

    public function testGetQueryBuilderAndIterator()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator();

        $abstractDBALFilterTypeMock = $this->createMock(AbstractDBALFilterType::class);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->any())
            ->method('getCurrentFilters')
            ->willReturn([
                new Filter('foo', ['type' => $abstractDBALFilterTypeMock, 'options' => []], 'uid'),
            ])
        ;

        $adminlistConfigurator->setFilterBuilder($filterBuilderMock);

        $requestMock = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([['page' => 1], [], ['_route' => 'testroute']])
            ->onlyMethods(['getSession'])
            ->getMock()
        ;

        $sessionMock = $this->createMock(Session::class);
        $sessionMock
            ->expects($this->once())
            ->method('has')
            ->willReturn(true)
        ;
        $sessionMock
            ->expects($this->once())
            ->method('get')
            ->with('listconfig_testroute')
            ->willReturn(['page' => 1, 'orderBy' => 'foo', 'orderDirection' => 'up'])
        ;

        $requestMock
            ->expects($this->exactly(2))
            ->method('getSession')
            ->willReturn($sessionMock)
        ;

        $adminlistConfigurator->bindRequest($requestMock);
        $this->assertInstanceOf(QueryBuilder::class, $adminlistConfigurator->getQueryBuilder());
        $this->assertInstanceOf(\Traversable::class, $adminlistConfigurator->getIterator());
    }

    public function testSetGetCountField()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator();

        $this->assertInstanceOf(AbstractDoctrineDBALAdminListConfigurator::class, $adminlistConfigurator->setCountField('foo'));
        $this->assertIsString($adminlistConfigurator->getCountField());
    }

    public function testSetGetDistinctCount()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator();

        $this->assertInstanceOf(AbstractDoctrineDBALAdminListConfigurator::class, $adminlistConfigurator->setUseDistinctCount(false));
        $this->assertFalse($adminlistConfigurator->getUseDistinctCount());
    }

    public function setUpAdminlistConfigurator(): AbstractDoctrineDBALAdminListConfigurator
    {
        return new class($this->connectionMock) extends AbstractDoctrineDBALAdminListConfigurator {
            public function buildFields(): void
            {
                $this->addField('hello', 'hello', true);
                $this->addField('world', 'world', true);
            }

            public function getEntityClass(): string
            {
                return \App\Entity\User::class;
            }
        };
    }
}

namespace App\Entity;

class User
{
}
