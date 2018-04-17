<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Configurator;

use DateTime;
use Exception;
use Kunstmaan\AdminBundle\Form\ColorType;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\SimpleBulkAction;
use Kunstmaan\AdminListBundle\AdminList\ListAction\SimpleListAction;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\LocaleBlacklistRule;
use PHPUnit_Framework_TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Kunstmaan\AdminListBundle\Tests\AdminList\ConcreteConfigurator;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-13 at 16:18:47.
 */
class AbstractAdminListConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ConcreteConfigurator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

        $session = $this->createMock(Session::class);
        $session->expects($this->any())->method('has')->willReturn(true);
        $session->expects($this->any())->method('get')->willReturn([
            'page' => 1,
            'orderBy' => 'id',
            'orderDirection' => 'ASC',
        ]);
        $request = new Request();
        $request->setSession($session);
        $request->query->add([
            '_route' => 'some-route',
        ]);
        $config = new ConcreteConfigurator();
        $config->bindRequest($request);
        $config->buildIterator();
        $this->object = $config;
    }

    public function testGetSet()
    {
        $config = $this->object;

        $config->setAdminTypeOptions(['test' => 123]);
        $config->setDeleteTemplate('delete.twig');
        $config->setEditTemplate('edit.twig');
        $config->setViewTemplate('view.twig');
        $config->setAddTemplate('add.twig');
        $config->setListTemplate('list.twig');

        $this->assertEquals('id', $config->getOrderBy());
        $this->assertEquals('ASC', $config->getOrderDirection());
        $this->assertEquals(1, $config->getPage());
        $this->assertEquals(10, $config->getLimit());
        $this->assertCount(1, $config->getAdminTypeOptions());
        $this->assertArrayHasKey('test', $config->getAdminTypeOptions());
        $viewUrl = $config->getViewUrlFor(['id' => 5]);
        $this->assertCount(2, $viewUrl);
        $this->assertArrayHasKey('path', $viewUrl);
        $this->assertArrayHasKey('params', $viewUrl);
        $this->assertArrayHasKey('id', $viewUrl['params']);
        $this->assertEquals('xyz:Xyz', $config->getControllerPath());
        $this->assertEquals(5, $viewUrl['params']['id']);
        $this->assertEquals('xyz_admin_xyz_view', $viewUrl['path']);
        $this->assertEquals('delete.twig', $config->getDeleteTemplate());
        $this->assertEquals('edit.twig', $config->getEditTemplate());
        $this->assertEquals('view.twig', $config->getViewTemplate());
        $this->assertEquals('add.twig', $config->getAddTemplate());
        $this->assertEquals('list.twig', $config->getListTemplate());
        $this->assertInstanceOf(DateTime::class, $config->decorateNewEntity(new DateTime()));
    }

    public function testGetAdminType()
    {
        $config = $this->object;
        $blackList = new LocaleBlacklistRule();

        $this->assertEquals('kunstmaan_lead_generation.rule.form.localeblacklistrule', $config->getAdminType($blackList));

        $this->setUp();
        $config = $this->object;
        $config->setAdminType(new ColorType());
        $this->assertInstanceOf(ColorType::class, $config->getAdminType($blackList));

        $this->setUp();
        $this->expectException(Exception::class);
        $this->object->getAdminType(new stdClass());
    }

    public function testGetSetHasBulkAction()
    {
        $config = $this->object;
        $this->assertFalse($config->hasBulkActions());
        $config->addBulkAction(new SimpleBulkAction(['/url'], 'label'));
        $this->assertTrue($config->hasBulkActions());
        $this->assertCount(1, $config->getBulkActions());
        $this->assertInstanceOf(SimpleBulkAction::class, $config->getBulkActions()[0]);
    }

    public function testGetSetHasListAction()
    {
        $config = $this->object;
        $this->assertFalse($config->hasListActions());
        $config->addListAction(new SimpleListAction(['/url'], 'label'));
        $this->assertTrue($config->hasListActions());
        $this->assertCount(1, $config->getListActions());
        $this->assertInstanceOf(SimpleListAction::class, $config->getListActions()[0]);
    }

    public function testGetSetHasItemAction()
    {
        $config = $this->object;
        $this->assertFalse($config->hasItemActions());
        $config->addSimpleItemAction('x', 'y', 'z');
        $this->assertTrue($config->hasItemActions());
    }

    public function testSortFields()
    {
        $config = $this->object;
        $this->assertCount(0, $config->getSortFields());
        $config->addField('sort', 'blah', true);
        $this->assertCount(1, $config->getSortFields());
    }

    public function testExportFields()
    {
        $config = $this->object;
        $config->buildExportFields();
        $this->assertCount(0, $config->getExportFields());
        $config->addExportField('sort', 'blah', 'blah.twig');
        $this->assertCount(1, $config->getExportFields());
        $config->resetBuilds();
        $this->assertCount(0, $config->getExportFields());
    }
}
