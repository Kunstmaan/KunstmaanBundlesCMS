<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\UnitOfWork;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\BulkActionInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;
use Kunstmaan\AdminListBundle\AdminList\ListAction\ListActionInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AbstractAdminListConfiguratorTest extends TestCase
{
    /** @var AbstractAdminListConfigurator */
    private $abstractAdminListConfMock;

    protected function setUp(): void
    {
        $this->abstractAdminListConfMock = $this->getMockForAbstractClass(AbstractAdminListConfigurator::class);
        $this->abstractAdminListConfMock
            ->expects($this->any())
            ->method('getBundleName')
            ->willReturn('Bundle')
        ;
        $this->abstractAdminListConfMock
            ->expects($this->any())
            ->method('getEntityName')
            ->willReturn('MyEntity')
        ;
    }

    public function testGetRepositoryName()
    {
        $this->assertEquals('Bundle:MyEntity', $this->abstractAdminListConfMock->getRepositoryName());
    }

    public function testBuildExportFields()
    {
        $this->abstractAdminListConfMock
            ->expects($this->once())
            ->method('buildFields')
            ->will($this->returnValue(['hello', 'world']))
        ;

        $this->assertNull($this->abstractAdminListConfMock->buildExportFields());
    }

    public function testResetBuilds()
    {
        $this->abstractAdminListConfMock->addField('name', 'header', true);
        $this->abstractAdminListConfMock->resetBuilds();
        $this->assertCount(0, $this->abstractAdminListConfMock->getFields());
    }

    public function testGetAddUrlFor()
    {
        $addUrl = $this->abstractAdminListConfMock->getAddUrlFor(['paramTest']);
        $this->assertArrayHasKey('My Entity', $addUrl);
        $this->assertArrayHasKey('path', $addUrl['My Entity']);
        $this->assertArrayHasKey('params', $addUrl['My Entity']);
        $this->assertEquals('bundle_admin_myentity_add', $addUrl['My Entity']['path']);
        $this->assertContains('paramTest', $addUrl['My Entity']['params']);
    }

    public function testGetExportUrlFor()
    {
        $exportUrl = $this->abstractAdminListConfMock->getExportUrl();
        $this->assertArrayHasKey('path', $exportUrl);
        $this->assertArrayHasKey('params', $exportUrl);
        $this->assertEquals('bundle_admin_myentity_export', $exportUrl['path']);
        $this->assertArrayHasKey('_format', $exportUrl['params']);
        $this->assertEquals('csv', $exportUrl['params']['_format']);
    }

    public function testGetViewUrlFor()
    {
        //from array
        $item = ['id' => 999];
        $viewUrl = $this->abstractAdminListConfMock->getViewUrlFor($item);
        $this->assertArrayHasKey('path', $viewUrl);
        $this->assertArrayHasKey('params', $viewUrl);
        $this->assertEquals('bundle_admin_myentity_view', $viewUrl['path']);
        $this->assertArrayHasKey('id', $viewUrl['params']);
        $this->assertEquals('999', $viewUrl['params']['id']);

        // from object
        $item = new class() {
            public function getId()
            {
                return 3;
            }
        };
        $viewUrl = $this->abstractAdminListConfMock->getViewUrlFor($item);
        $this->assertArrayHasKey('params', $viewUrl);
        $this->assertEquals(3, $viewUrl['params']['id']);
    }

    public function testGetIndexUrl()
    {
        $indexUrl = $this->abstractAdminListConfMock->getIndexUrl();
        $this->assertArrayHasKey('path', $indexUrl);
        $this->assertArrayHasKey('params', $indexUrl);
        $this->assertEquals('bundle_admin_myentity', $indexUrl['path']);
        $this->assertIsArray($indexUrl['params']);
    }

    public function testGetAdminTypeExistsInEntity()
    {
        $entity = new class() {
            public function getAdminType()
            {
                return 'TestType';
            }
        };

        $this->assertEquals('TestType', $this->abstractAdminListConfMock->getAdminType($entity));
    }

    public function testGetAdminTypeAlreadySet()
    {
        $this->abstractAdminListConfMock->setAdminType('TestType');
        $this->assertEquals('TestType', $this->abstractAdminListConfMock->getAdminType(new \stdClass()));
    }

    public function testGetAdminTypeNotExistsInEntity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $entity = new \stdClass();
        $this->abstractAdminListConfMock->getAdminType($entity);
    }

    public function testSetAdminType()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setAdminType('TestType'));
    }

    public function testSetAdminTypeOptions()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setAdminTypeOptions([]));
    }

    public function testGetAdminTypeOptions()
    {
        $this->assertIsArray($this->abstractAdminListConfMock->getAdminTypeOptions());
    }

    public function testCanEdit()
    {
        $item = new \stdClass();
        $this->assertTrue($this->abstractAdminListConfMock->canEdit($item));
    }

    public function testCanDelete()
    {
        $item = new \stdClass();
        $this->assertTrue($this->abstractAdminListConfMock->canDelete($item));
    }

    public function testCanAdd()
    {
        $this->assertTrue($this->abstractAdminListConfMock->canAdd());
    }

    public function testCanView()
    {
        $item = new \stdClass();
        $this->assertFalse($this->abstractAdminListConfMock->canView($item));
    }

    public function testCanExport()
    {
        $this->assertFalse($this->abstractAdminListConfMock->canExport());
    }

    public function testAddField()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->addField('name', 'header', true));
        $this->assertCount(1, $this->abstractAdminListConfMock->getFields());
    }

    public function testAddExportField()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->addExportField('name', 'header', true));
        $exportFields = $this->abstractAdminListConfMock->getExportFields();
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
        $this->assertEquals(10, $this->abstractAdminListConfMock->getLimit());
    }

    public function testGetSortFields()
    {
        $this->abstractAdminListConfMock->addField('test', 'test', true);
        $sortFields = $this->abstractAdminListConfMock->getSortFields();
        $this->assertContains('test', $sortFields);
    }

    public function testGetFields()
    {
        $this->assertIsArray($this->abstractAdminListConfMock->getFields());
    }

    public function testGetExportFields()
    {
        $this->assertIsArray($this->abstractAdminListConfMock->getExportFields());
    }

    public function testAddSimpleItemAction()
    {
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->addSimpleItemAction('test', 'test', 'test'));
    }

    public function testAddHasGetItemAction()
    {
        $itemActionInterfaceMock = $this->createMock(ItemActionInterface::class);
        $this->abstractAdminListConfMock->addItemAction($itemActionInterfaceMock);
        $this->assertTrue($this->abstractAdminListConfMock->hasItemActions());
        $this->assertContainsOnlyInstancesOf(ItemActionInterface::class, $this->abstractAdminListConfMock->getItemActions());
    }

    public function testAddHasGetListAction()
    {
        $listActionInterfaceMock = $this->createMock(ListActionInterface::class);
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->addListAction($listActionInterfaceMock));
        $this->assertTrue($this->abstractAdminListConfMock->hasListActions());
        $this->assertContainsOnlyInstancesOf(ListActionInterface::class, $this->abstractAdminListConfMock->getListActions());
    }

    public function testAddHasGetBulkAction()
    {
        $bulkActionInterfaceMock = $this->createMock(BulkActionInterface::class);
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->addBulkAction($bulkActionInterfaceMock));
        $this->assertTrue($this->abstractAdminListConfMock->hasBulkActions());
        $this->assertContainsOnlyInstancesOf(BulkActionInterface::class, $this->abstractAdminListConfMock->getBulkActions());
    }

    public function testGetListTemplate()
    {
        $this->assertEquals('@KunstmaanAdminList/Default/list.html.twig', $this->abstractAdminListConfMock->getListTemplate());
    }

    public function testSetListTemplate()
    {
        $template = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setListTemplate($template));
        $this->assertEquals($template, $this->abstractAdminListConfMock->getListTemplate());
    }

    public function testGetValue()
    {
        $columnName = 'foo';
        $this->assertEquals('bar', $this->abstractAdminListConfMock->getValue(['foo' => 'bar'], $columnName));
        $this->assertEquals('', $this->abstractAdminListConfMock->getValue(['foz' => 'bar'], $columnName));

        $item = new class() {
            public function getFoo()
            {
                return 'bar';
            }
        };

        $this->assertEquals('bar', $this->abstractAdminListConfMock->getValue($item, $columnName));
        $this->assertEquals(sprintf('undefined function [get/is/has]%s()', $columnName), $this->abstractAdminListConfMock->getValue(new \stdClass(), $columnName));
    }

    public function testgetStringValue()
    {
        // value = string
        $columnName = 'foo';
        $this->assertEquals('true', $this->abstractAdminListConfMock->getStringValue(['foo' => true], $columnName));

        // value = DateTime
        $value = new \DateTime();
        $this->assertEquals($value->format('Y-m-d H:i:s'), $this->abstractAdminListConfMock->getStringValue(['foo' => $value], $columnName));

        // value = empty PersistentCollection
        $emMock = $this->createMock(EntityManagerInterface::class);
        $value = new PersistentCollection($emMock, 'ClassName', new ArrayCollection());
        $this->assertEquals('', $this->abstractAdminListConfMock->getStringValue(['foo' => $value], $columnName));

        // value = PersistentCollection
        $emMock = $this->createMock(EntityManagerInterface::class);
        $emMock
            ->expects($this->any())
            ->method('getUnitOfWork')
            ->willReturn($this->createMock(UnitOfWork::class))
        ;

        $value = new PersistentCollection($emMock, 'ClassName', new ArrayCollection());
        $value->add(new class() {
            public function getName()
            {
                return 'bar';
            }
        });
        $value->add(new class() {
            public function getName()
            {
                return 'baz';
            }
        });
        $this->assertEquals('bar, baz', $this->abstractAdminListConfMock->getStringValue(['foo' => $value], $columnName));

        // value = array
        $value = ['bar', 'baz'];
        $this->assertEquals('bar, baz', $this->abstractAdminListConfMock->getStringValue(['foo' => $value], $columnName));

        // value = non of the above
        $value = 'baz';
        $this->assertEquals('baz', $this->abstractAdminListConfMock->getStringValue(['foo' => $value], $columnName));
    }

    public function testSetGetAddTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setAddTemplate($value));
        $this->assertEquals($value, $this->abstractAdminListConfMock->getAddTemplate());
    }

    public function testSetGetViewTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setViewTemplate($value));
        $this->assertEquals($value, $this->abstractAdminListConfMock->getViewTemplate());
    }

    public function testSetGetEditTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setEditTemplate($value));
        $this->assertEquals($value, $this->abstractAdminListConfMock->getEditTemplate());
    }

    public function testSetGetDeleteTemplate()
    {
        $value = 'test_template';
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setDeleteTemplate($value));
        $this->assertEquals($value, $this->abstractAdminListConfMock->getDeleteTemplate());
    }

    public function testDecorateNewEntity()
    {
        $this->assertInstanceOf(\stdClass::class, $this->abstractAdminListConfMock->decorateNewEntity(new \stdClass()));
    }

    public function testGetFilterBuilder()
    {
        // test without existsing FilterBuilder
        $this->assertInstanceOf(FilterBuilder::class, $this->abstractAdminListConfMock->getFilterBuilder());

        // test with first a set
        $this->assertInstanceOf(AbstractAdminListConfigurator::class, $this->abstractAdminListConfMock->setFilterBuilder(new FilterBuilder()));
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
            ->setMethods(['getSession'])
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

        $abstractAdminListConfMock->bindRequest($requestMock);
    }

    public function testGetPage()
    {
        $this->assertIsInt($this->abstractAdminListConfMock->getPage());
    }

    public function testGetOrderBy()
    {
        $this->assertIsString($this->abstractAdminListConfMock->getOrderBy());
    }

    public function testGetOrderDirection()
    {
        $this->assertIsString($this->abstractAdminListConfMock->getOrderDirection());
    }

    public function testGetPathByConvention()
    {
        $this->assertEquals('bundle_admin_myentity_test', $this->abstractAdminListConfMock->getPathByconvention('test'));
    }

    public function testGetControllerPath()
    {
        $this->assertEquals('Bundle:MyEntity', $this->abstractAdminListConfMock->getControllerPath());
    }

    public function testGetExtraParameters()
    {
        $this->assertIsArray($this->abstractAdminListConfMock->getExtraParameters());
    }
}
