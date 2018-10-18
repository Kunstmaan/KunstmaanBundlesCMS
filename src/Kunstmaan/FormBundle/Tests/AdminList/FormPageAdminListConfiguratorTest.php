<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\NodeBundle\Tests\Stubs\TestRepository;
use Kunstmaan\FormBundle\Tests\Stubs\TestConfiguration;
use Kunstmaan\FormBundle\Tests\Entity\FakePage;

/**
 * This test tests the FormPageAdminListConfigurator
 */
class FormPageAdminListConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    const PERMISSION_VIEW = 'view';

    /**
     * @var FormPageAdminListConfigurator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $em = $this->getMockedEntityManager();
        $tokenStorage = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        $roleHierarchy = $this->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchyInterface')
          ->getMock();
        $aclHelper = $this->createMock('Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper', array(), array($em, $tokenStorage, $roleHierarchy));

        $this->object = new FormPageAdminListConfigurator($em, $aclHelper, self::PERMISSION_VIEW);
    }

    /**
     * https://gist.github.com/1331789
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $emMock  = $this->createMock('\Doctrine\ORM\EntityManager', array('getRepository', 'getConfiguration', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new TestRepository()));
        $emMock->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue(new TestConfiguration()));
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object) array('name' => 'aClass')));
        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        return $emMock;  // it tooks 13 lines to achieve mock!
    }

    public function testAdaptQueryBuilder()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->any())
            ->method('innerJoin')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->any())
            ->method('andWhere')
            ->will($this->returnSelf());

        /* @var $queryBuilder QueryBuilder */
        $this->object->adaptQueryBuilder($queryBuilder);
    }



    public function testFixedGetters()
    {
        $item = new FakePage();
        $item->setId(123);
        $this->assertEquals('', $this->object->getAddUrlFor([]));
        $this->assertEquals('KunstmaanNodeBundle', $this->object->getBundleName());
        $this->assertEquals('NodeTranslation', $this->object->getEntityName());
        $this->assertEquals('KunstmaanFormBundle:FormSubmissions', $this->object->getControllerPath());
        $this->assertCount(0, $this->object->getDeleteUrlFor($item));
        $this->assertCount(1, $this->object->getIndexUrl());
        $this->assertCount(2, $this->object->getEditUrlFor($item));
        $this->assertFalse($this->object->canAdd());
        $this->assertFalse($this->object->canEdit($item));
        $this->assertFalse($this->object->canDelete($item));
    }



    public function testBuildFilters()
    {
        $this->object->buildFilters();
        $filters = $this->object->getFilterBuilder()->getFilterDefinitions();
        $this->assertCount(2, $filters);
    }

    public function testBuildFields()
    {
        $this->object->buildFields();
        $fields = $this->object->getFields();
        $this->assertCount(3, $fields);
    }

    public function testBuildItemActions()
    {
        $item = new FakePage();
        $item->setId(1);
        $this->object->buildItemActions();
        $actions = $this->object->getItemActions();
        $this->assertCount(1, $actions);
        $this->assertInstanceOf(SimpleItemAction::class, $actions[0]);
        /** @var SimpleItemAction $action */
        $action = $actions[0];
        $this->assertCount(2, $action->getUrlFor($item));
    }
}
