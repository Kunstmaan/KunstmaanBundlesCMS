<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use App\Entity\News;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\BulkActionInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;
use Kunstmaan\AdminListBundle\AdminList\ListAction\ListActionInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AbstractAdminListConfiguratorTest extends TestCase
{
    /** @var AbstractAdminListConfigurator */
    private $adminListConfigurator;

    protected function setUp(): void
    {
        $this->adminListConfigurator = new class extends AbstractAdminListConfigurator {
            public function buildFields()
            {
                $this->addField('hello', 'hello', true);
                $this->addField('world', 'world', true);
            }

            public function getEditUrlFor($item): array
            {
                return [];
            }

            public function getDeleteUrlFor($item): array
            {
                return [];
            }

            public function getCount(): int
            {
                return 0;
            }

            public function getItems(): array
            {
                return [];
            }

            public function getPagerfanta(): Pagerfanta
            {
                return new Pagerfanta(new ArrayAdapter([]));
            }

            public function getIterator(): iterable
            {
                return [];
            }

            public function getEntityClass(): string
            {
                return \App\Entity\News::class;
            }
        };
    }

    public function testGetRepositoryName()
    {
        $this->assertEquals(News::class, $this->adminListConfigurator->getRepositoryName());
    }

    public function testBuildExportFields()
    {
        $this->adminListConfigurator->buildExportFields();

        $exportFields = $this->adminListConfigurator->getExportFields();
        $this->assertCount(2, $exportFields);
        $this->assertSame('hello', $exportFields[0]->getName());
        $this->assertSame('world', $exportFields[1]->getName());
    }

    public function testResetBuilds()
    {
        $this->adminListConfigurator->addField('name', 'header', true);
        $this->adminListConfigurator->resetBuilds();
        $this->assertCount(0, $this->adminListConfigurator->getFields());
    }

    public function testGetAddUrlFor()
    {
        $addUrl = $this->adminListConfigurator->getAddUrlFor(['paramTest']);
        $this->assertArrayHasKey('News', $addUrl);
        $this->assertArrayHasKey('path', $addUrl['News']);
        $this->assertArrayHasKey('params', $addUrl['News']);
        $this->assertEquals('app_admin_news_add', $addUrl['News']['path']);
        $this->assertContains('paramTest', $addUrl['News']['params']);
    }

    public function testGetExportUrlFor()
    {
        $exportUrl = $this->adminListConfigurator->getExportUrl();
        $this->assertArrayHasKey('path', $exportUrl);
        $this->assertArrayHasKey('params', $exportUrl);
        $this->assertEquals('app_admin_news_export', $exportUrl['path']);
        $this->assertArrayHasKey('_format', $exportUrl['params']);
        $this->assertEquals('csv', $exportUrl['params']['_format']);
    }

    public function testGetViewUrlFor()
    {
        // from array
        $item = ['id' => 999];
        $viewUrl = $this->adminListConfigurator->getViewUrlFor($item);
        $this->assertArrayHasKey('path', $viewUrl);
        $this->assertArrayHasKey('params', $viewUrl);
        $this->assertEquals('app_admin_news_view', $viewUrl['path']);
        $this->assertArrayHasKey('id', $viewUrl['params']);
        $this->assertEquals('999', $viewUrl['params']['id']);

        // from object
        $item = new class {
            public function getId()
            {
                return 3;
            }
        };
        $viewUrl = $this->adminListConfigurator->getViewUrlFor($item);
        $this->assertArrayHasKey('params', $viewUrl);
        $this->assertEquals(3, $viewUrl['params']['id']);
    }

    public function testGetIndexUrl()
    {
        $indexUrl = $this->adminListConfigurator->getIndexUrl();
        $this->assertArrayHasKey('path', $indexUrl);
        $this->assertArrayHasKey('params', $indexUrl);
        $this->assertEquals('app_admin_news', $indexUrl['path']);
        $this->assertIsArray($indexUrl['params']);
    }

    public function testGetAdminTypeExistsInEntity()
    {
        $entity = new class {
            public function getAdminType()
            {
                return 'TestType';
            }
        };

        $this->assertEquals('TestType', $this->adminListConfigurator->getAdminType($entity));
    }

    public function testGetAdminTypeAlreadySet()
    {
        $this->adminListConfigurator->setAdminType('TestType');
        $this->assertEquals('TestType', $this->adminListConfigurator->getAdminType(new \stdClass()));
    }

    public function testGetAdminTypeNotExistsInEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $entity = new \stdClass();
        $this->adminListConfigurator->getAdminType($entity);
    }

    public function testSetAdminType()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setAdminType('TestType'));
    }

    public function testSetAdminTypeOptions()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setAdminTypeOptions([]));
    }

    public function testGetAdminTypeOptions()
    {
        $this->assertIsArray($this->adminListConfigurator->getAdminTypeOptions());
    }

    public function testCanEdit()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminListConfigurator->canEdit($item));
    }

    public function testCanDelete()
    {
        $item = new \stdClass();
        $this->assertTrue($this->adminListConfigurator->canDelete($item));
    }

    public function testCanAdd()
    {
        $this->assertTrue($this->adminListConfigurator->canAdd());
    }

    public function testCanView()
    {
        $item = new \stdClass();
        $this->assertFalse($this->adminListConfigurator->canView($item));
    }

    public function testCanExport()
    {
        $this->assertFalse($this->adminListConfigurator->canExport());
    }

    public function testAddField()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->addField('name', 'header', true));
        $this->assertCount(1, $this->adminListConfigurator->getFields());
    }

    public function testAddExportField()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->addExportField('name', 'header', true));
        $exportFields = $this->adminListConfigurator->getExportFields();
        $this->assertCount(1, $exportFields);

        $this->assertFalse($exportFields[0]->isSortable());
    }

    public function testAddFilter()
    {
        $abstractAdminListConfMock = $this->getMockForAbstractClass(AbstractAdminListConfigurator::class, [], '', true, true, true, ['getFilterBuilder']);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->once())
            ->method('add')
            ->willReturn(FilterBuilder::class)
        ;

        $abstractAdminListConfMock
            ->expects($this->once())
            ->method('getFilterBuilder')
            ->willReturn($filterBuilderMock)
        ;

        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $abstractAdminListConfMock->addFilter('testColumn'));
    }

    public function testGetLimit()
    {
        $this->assertEquals(10, $this->adminListConfigurator->getLimit());
    }

    public function testGetSortFields()
    {
        $this->adminListConfigurator->addField('test', 'test', true);
        $sortFields = $this->adminListConfigurator->getSortFields();
        $this->assertContains('test', $sortFields);
    }

    public function testGetFields()
    {
        $this->assertIsArray($this->adminListConfigurator->getFields());
    }

    public function testGetExportFields()
    {
        $this->assertIsArray($this->adminListConfigurator->getExportFields());
    }

    public function testAddSimpleItemAction()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->addSimpleItemAction('test', 'test', 'test'));
    }

    public function testAddHasGetItemAction()
    {
        $itemActionInterfaceMock = $this->createMock(ItemActionInterface::class);
        $this->adminListConfigurator->addItemAction($itemActionInterfaceMock);
        $this->assertTrue($this->adminListConfigurator->hasItemActions());
        $this->assertContainsOnlyInstancesOf(ItemActionInterface::class, $this->adminListConfigurator->getItemActions());
    }

    public function testAddHasGetListAction()
    {
        $listActionInterfaceMock = $this->createMock(ListActionInterface::class);
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->addListAction($listActionInterfaceMock));
        $this->assertTrue($this->adminListConfigurator->hasListActions());
        $this->assertContainsOnlyInstancesOf(ListActionInterface::class, $this->adminListConfigurator->getListActions());
    }

    public function testAddHasGetBulkAction()
    {
        $bulkActionInterfaceMock = $this->createMock(BulkActionInterface::class);
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->addBulkAction($bulkActionInterfaceMock));
        $this->assertTrue($this->adminListConfigurator->hasBulkActions());
        $this->assertContainsOnlyInstancesOf(BulkActionInterface::class, $this->adminListConfigurator->getBulkActions());
    }

    public function testGetListTemplate()
    {
        $this->assertEquals('@KunstmaanAdminList/Default/list.html.twig', $this->adminListConfigurator->getListTemplate());
    }

    public function testSetListTemplate()
    {
        $template = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setListTemplate($template));
        $this->assertEquals($template, $this->adminListConfigurator->getListTemplate());
    }

    public function testGetValue()
    {
        $columnName = 'foo';
        $this->assertEquals('bar', $this->adminListConfigurator->getValue(['foo' => 'bar'], $columnName));
        $this->assertEquals('', $this->adminListConfigurator->getValue(['foz' => 'bar'], $columnName));

        $item = new class {
            public function getFoo()
            {
                return 'bar';
            }
        };

        $this->assertEquals('bar', $this->adminListConfigurator->getValue($item, $columnName));
        $this->assertEquals(sprintf('undefined function [get/is/has]%s()', $columnName), $this->adminListConfigurator->getValue(new \stdClass(), $columnName));
    }

    public function testgetStringValue()
    {
        // value = string
        $columnName = 'foo';
        $this->assertEquals('true', $this->adminListConfigurator->getStringValue(['foo' => true], $columnName));

        // value = DateTime
        $value = new \DateTime();
        $this->assertEquals($value->format('Y-m-d H:i:s'), $this->adminListConfigurator->getStringValue(['foo' => $value], $columnName));

        // value = empty PersistentCollection
        $emMock = $this->createMock(EntityManagerInterface::class);
        $value = new PersistentCollection($emMock, 'ClassName', new ArrayCollection());
        $this->assertEquals('', $this->adminListConfigurator->getStringValue(['foo' => $value], $columnName));

        // value = PersistentCollection
        $emMock = $this->createMock(EntityManagerInterface::class);
        $emMock
            ->expects($this->any())
            ->method('getUnitOfWork')
            ->willReturn($this->createMock(UnitOfWork::class))
        ;

        $value = new PersistentCollection($emMock, 'ClassName', new ArrayCollection());
        $value->add(new class {
            public function getName()
            {
                return 'bar';
            }
        });
        $value->add(new class {
            public function getName()
            {
                return 'baz';
            }
        });
        $this->assertEquals('bar, baz', $this->adminListConfigurator->getStringValue(['foo' => $value], $columnName));

        // value = array
        $value = ['bar', 'baz'];
        $this->assertEquals('bar, baz', $this->adminListConfigurator->getStringValue(['foo' => $value], $columnName));

        // value = non of the above
        $value = 'baz';
        $this->assertEquals('baz', $this->adminListConfigurator->getStringValue(['foo' => $value], $columnName));
    }

    public function testSetGetAddTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setAddTemplate($value));
        $this->assertEquals($value, $this->adminListConfigurator->getAddTemplate());
    }

    public function testSetGetViewTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setViewTemplate($value));
        $this->assertEquals($value, $this->adminListConfigurator->getViewTemplate());
    }

    public function testSetGetEditTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setEditTemplate($value));
        $this->assertEquals($value, $this->adminListConfigurator->getEditTemplate());
    }

    public function testSetGetDeleteTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setDeleteTemplate($value));
        $this->assertEquals($value, $this->adminListConfigurator->getDeleteTemplate());
    }

    public function testDecorateNewEntity()
    {
        $this->assertInstanceOf(\stdClass::class, $this->adminListConfigurator->decorateNewEntity(new \stdClass()));
    }

    public function testGetFilterBuilder()
    {
        // test without existsing FilterBuilder
        $this->assertInstanceOf(FilterBuilder::class, $this->adminListConfigurator->getFilterBuilder());

        // test with first a set
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->adminListConfigurator->setFilterBuilder(new FilterBuilder()));
    }

    public function testBindRequestWithoutExistingSession()
    {
        $abstractAdminListConfMock = $this->getMockForAbstractClass(AbstractAdminListConfigurator::class, [], '', true, true, true, ['getFilterBuilder']);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->once())
            ->method('bindRequest')
        ;

        $abstractAdminListConfMock
            ->expects($this->once())
            ->method('getFilterBuilder')
            ->willReturn($filterBuilderMock)
        ;

        $requestMock = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([['page' => 1], [], ['_route' => 'testroute']])
            ->onlyMethods(['getSession'])
            ->getMock()
        ;
        $sessionMock = $this->createMock(Session::class);

        $requestMock
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($sessionMock)
        ;

        $abstractAdminListConfMock->bindRequest($requestMock);
    }

    public function testBindRequestWithExistingSession()
    {
        $abstractAdminListConfMock = $this->getMockForAbstractClass(AbstractAdminListConfigurator::class, [], '', true, true, true, ['getFilterBuilder']);

        $filterBuilderMock = $this->createMock(FilterBuilder::class);
        $filterBuilderMock
            ->expects($this->once())
            ->method('bindRequest')
        ;

        $abstractAdminListConfMock
            ->expects($this->once())
            ->method('getFilterBuilder')
            ->willReturn($filterBuilderMock)
        ;

        $requestMock = $this->getMockBuilder(Request::class)
            ->setConstructorArgs([[], [], ['_route' => 'testroute']])
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

        $abstractAdminListConfMock->bindRequest($requestMock);
    }

    public function testGetPage()
    {
        $this->assertIsInt($this->adminListConfigurator->getPage());
    }

    public function testGetOrderBy()
    {
        $this->assertIsString($this->adminListConfigurator->getOrderBy());
    }

    public function testGetOrderDirection()
    {
        $this->assertIsString($this->adminListConfigurator->getOrderDirection());
    }

    public function testGetPathByConvention()
    {
        $this->assertEquals('app_admin_news_test', $this->adminListConfigurator->getPathByconvention('test'));
    }

    public function testGetExtraParameters()
    {
        $this->assertIsArray($this->adminListConfigurator->getExtraParameters());
    }
}

namespace App\Entity;

class News
{
}
