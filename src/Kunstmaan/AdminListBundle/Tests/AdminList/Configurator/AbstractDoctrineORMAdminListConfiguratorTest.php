<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Filter;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\AbstractORMFilterType;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AbstractDoctrineORMAdminListConfiguratorTest extends TestCase
{
    /** @var EntityManagerInterface */
    private $emMock;

    /** @var AclHelper */
    private $aclHelperMock;

    public function setUp(): void
    {
        $queryMock = $this->createMock(AbstractQuery::class);
        $queryMock
            ->expects($this->any())
            ->method('iterate')
            ->willReturn($this->createMock(\Iterator::class))
        ;

        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->aclHelperMock = $this->createMock(AclHelper::class);
        $this->aclHelperMock
            ->expects($this->any())
            ->method('apply')
            ->willReturn($queryMock)
        ;

        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $queryBuilderMock
            ->expects($this->any())
            ->method('getQuery')
            ->willReturn($queryMock)
        ;

        $entityRepositoryMock = $this->createMock(EntityRepository::class);
        $entityRepositoryMock
            ->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilderMock)
        ;

        $this->emMock
            ->expects($this->any())
            ->method('getRepository')
            ->with('App\\Entity\\Blog')
            ->willReturn($entityRepositoryMock)
        ;
        $this->emMock
            ->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($this->createMock(ClassMetadata::class))
        ;
    }

    public function testGetEditUrl()
    {
        $abstractMock = $this->setUpAdminlistConfigurator();

        $item = new class {
            public function getId()
            {
                return 747;
            }
        };

        $editUrl = $abstractMock->getEditUrlFor($item);
        $this->assertIsArray($editUrl);
        $this->assertArrayHasKey('path', $editUrl);
        $this->assertArrayHasKey('params', $editUrl);
        $this->assertEquals('app_admin_blog_' . AbstractDoctrineORMAdminListConfigurator::SUFFIX_EDIT, $editUrl['path']);
        $this->assertContains(747, $editUrl['params']);
    }

    public function testGetDeleteUrl()
    {
        $abstractMock = $this->setUpAdminlistConfigurator();

        $item = new class {
            public function getId()
            {
                return 747;
            }
        };

        $editUrl = $abstractMock->getDeleteUrlFor($item);
        $this->assertIsArray($editUrl);
        $this->assertArrayHasKey('path', $editUrl);
        $this->assertArrayHasKey('params', $editUrl);
        $this->assertEquals('app_admin_blog_' . AbstractDoctrineORMAdminListConfigurator::SUFFIX_DELETE, $editUrl['path']);
        $this->assertContains(747, $editUrl['params']);
    }

    public function testGetPagerFanta()
    {
        $abstractMock = $this->setUpAdminlistConfigurator();

        $this->assertInstanceOf(Pagerfanta::class, $abstractMock->getPagerfanta());
    }

    public function testGetQueryDefault()
    {
        $abstractMock = $this->setUpAdminlistConfigurator();

        // default
        $this->assertInstanceOf(AbstractQuery::class, $abstractMock->getQuery());

        // no longer null, direct return
        $this->assertInstanceOf(AbstractQuery::class, $abstractMock->getQuery());

        // check the iterator in one go
        $this->assertInstanceOf(\Iterator::class, $abstractMock->getIterator());
    }

    public function testGetQueryWithFilter()
    {
        $abstractORMFilterTypeMock = $this->createMock(AbstractORMFilterType::class);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->any())
            ->method('getCurrentFilters')
            ->willReturn([
                new Filter('foo', ['type' => $abstractORMFilterTypeMock, 'options' => []], 'uid'),
            ])
        ;

        $adminlistConfigurator = $this->setUpAdminlistConfigurator(['getFilterBuilder']);
        $adminlistConfigurator->setFilterBuilder($filterBuilderMock);

        $adminlistConfigurator->addFilter('foo');
        $this->assertInstanceOf(AbstractQuery::class, $adminlistConfigurator->getQuery());
    }

    public function testGetQueryWithOrderBy()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator(['getFilterBuilder']);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->once())
            ->method('bindRequest')
        ;
        $filterBuilderMock
            ->expects($this->any())
            ->method('getCurrentFilters')
            ->willReturn([])
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
        $this->assertInstanceOf(AbstractQuery::class, $adminlistConfigurator->getQuery());
    }

    public function testGetQueryWithPermissionDefAndAcl()
    {
        $abstractMock = $this->setUpAdminlistConfigurator();

        /** @var PermissionDefinition $permissionDefinitionMock */
        $permissionDefinitionMock = $this->createMock(PermissionDefinition::class);
        $this->assertInstanceOf(AbstractDoctrineORMAdminListConfigurator::class, $abstractMock->setPermissionDefinition($permissionDefinitionMock));
        $this->assertInstanceOf(AbstractQuery::class, $abstractMock->getQuery());
    }

    public function testSetGetPermissionDefinition()
    {
        $abstractMock = $this->setUpAdminlistConfigurator();

        /** @var PermissionDefinition $permissionDefinitionMock */
        $permissionDefinitionMock = $this->createMock(PermissionDefinition::class);
        $this->assertInstanceOf(AbstractDoctrineORMAdminListConfigurator::class, $abstractMock->setPermissionDefinition($permissionDefinitionMock));
        $this->assertInstanceOf(PermissionDefinition::class, $abstractMock->getPermissionDefinition());
    }

    public function testSetGetEntityManager()
    {
        $adminlistConfigurator = $this->setUpAdminlistConfigurator();

        /** @var EntityManagerInterface $emMock */
        $emMock = $this->createMock(EntityManagerInterface::class);
        $this->assertInstanceOf(AbstractDoctrineORMAdminListConfigurator::class, $adminlistConfigurator->setEntityManager($emMock));
        $this->assertInstanceOf(EntityManagerInterface::class, $adminlistConfigurator->getEntityManager());
    }

    public function setUpAdminlistConfigurator(): AbstractDoctrineORMAdminListConfigurator
    {
        return new class($this->emMock) extends AbstractDoctrineORMAdminListConfigurator {
            public function buildFields()
            {
                $this->addField('hello', 'hello', true);
                $this->addField('world', 'world', true);
            }

            public function getEntityClass(): string
            {
                return \App\Entity\Blog::class;
            }
        };
    }
}

namespace App\Entity;

class Blog
{
}
