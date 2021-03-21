<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
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
        $this->connectionMock
            ->expects($this->any())
            ->method('executeQuery')
            ->willReturn($this->createMock(Statement::class))
        ;
    }

    public function testGetEditUrl()
    {
        $abstractMock = $this->setUpAbstractMock();

        $editUrl = $abstractMock->getEditUrlFor(['id' => 888]);
        $this->assertIsArray($editUrl);
        $this->assertArrayHasKey('path', $editUrl);
        $this->assertArrayHasKey('params', $editUrl);
        $this->assertEquals('bundle_admin_myentity_' . AbstractDoctrineDBALadminListConfigurator::SUFFIX_EDIT, $editUrl['path']);
        $this->assertContains(888, $editUrl['params']);
    }

    public function testGetDeleteUrl()
    {
        $abstractMock = $this->setUpAbstractMock();

        $editUrl = $abstractMock->getDeleteUrlFor(['id' => 888]);
        $this->assertIsArray($editUrl);
        $this->assertArrayHasKey('path', $editUrl);
        $this->assertArrayHasKey('params', $editUrl);
        $this->assertEquals('bundle_admin_myentity_' . AbstractDoctrineDBALAdminListConfigurator::SUFFIX_DELETE, $editUrl['path']);
        $this->assertContains(888, $editUrl['params']);
    }

    public function testGetPagerFanta()
    {
        $abstractMock = $this->setUpAbstractMock();
        $this->assertInstanceOf(Pagerfanta::class, $abstractMock->getPagerfanta());
    }

    public function testGetQueryBuilderAndIterator()
    {
        $abstractMock = $this->setUpAbstractMock(['getFilterBuilder']);

        $abstractDBALFilterTypeMock = $this->createMock(AbstractDBALFilterType::class);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->any())
            ->method('getCurrentFilters')
            ->willReturn([
                new Filter('foo', ['type' => $abstractDBALFilterTypeMock, 'options' => []], 'uid'),
            ])
        ;

        $abstractMock
            ->expects($this->any())
            ->method('getFilterBuilder')
            ->willReturn($filterBuilderMock)
        ;

        $requestMock = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([['page' => 1], [], ['_route' => 'testroute']])
            ->setMethods(['getSession'])
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

        $abstractMock->bindRequest($requestMock);
        $this->assertInstanceOf(QueryBuilder::class, $abstractMock->getQueryBuilder());
        $this->assertInstanceOf(\Traversable::class, $abstractMock->getIterator());
    }

    public function testSetGetCountField()
    {
        $abstractMock = $this->setUpAbstractMock();

        $this->assertInstanceOf(AbstractDoctrineDBALAdminListConfigurator::class, $abstractMock->setCountField('foo'));
        $this->assertIsString($abstractMock->getCountField());
    }

    public function testSetGetDistinctCount()
    {
        $abstractMock = $this->setUpAbstractMock();

        $this->assertInstanceOf(AbstractDoctrineDBALAdminListConfigurator::class, $abstractMock->setUseDistinctCount(false));
        $this->assertFalse($abstractMock->getUseDistinctCount());
    }

    public function setUpAbstractMock(array $methods = [])
    {
        /** @var AbstractDoctrineDBALAdminListConfigurator $abstractMock */
        $abstractMock = $this->getMockForAbstractClass(AbstractDoctrineDBALAdminListConfigurator::class, [$this->connectionMock], '', true, true, true, $methods);
        $abstractMock
            ->expects($this->any())
            ->method('getBundleName')
            ->willReturn('Bundle')
        ;
        $abstractMock
            ->expects($this->any())
            ->method('getEntityName')
            ->willReturn('MyEntity')
        ;

        return $abstractMock;
    }
}
