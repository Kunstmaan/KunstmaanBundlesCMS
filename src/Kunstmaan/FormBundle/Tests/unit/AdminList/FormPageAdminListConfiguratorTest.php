<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Doctrine\ORM\Configuration;
use Codeception\Stub;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;

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
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $configuration = Stub::make(Configuration::class, [
            'getQuoteStrategy' => null,
        ]);

        $repository = Stub::make(EntityRepository::class, [
            'find' => null,
            'findBy' => null,
            'findOneBy' => null,
        ]);
        /** @var \Doctrine\ORM\EntityManager $emMock */
        $emMock = Stub::make(EntityManager::class, [
            'getRepository' => $repository,
            'getClassMetaData' => (object) ['name' => 'aClass'],
            'getConfiguration' => $configuration,
            'persist' => null,
            'flush' => null,
        ]);

        return $emMock;
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
        $item = Stub::makeEmpty(AbstractPage::class, ['getId' => 123]);
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
        $item = Stub::makeEmpty(AbstractPage::class, ['getId' => 123]);
        $this->object->buildItemActions();
        $actions = $this->object->getItemActions();
        $this->assertCount(1, $actions);
        $this->assertInstanceOf(SimpleItemAction::class, $actions[0]);
        /** @var SimpleItemAction $action */
        $action = $actions[0];
        $this->assertCount(2, $action->getUrlFor($item));
    }
}
