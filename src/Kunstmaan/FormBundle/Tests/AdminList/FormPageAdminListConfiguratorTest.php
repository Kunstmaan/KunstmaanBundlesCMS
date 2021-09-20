<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use PHPUnit\Framework\TestCase;

class FormPageAdminListConfiguratorTest extends TestCase
{
    const PERMISSION_VIEW = 'view';

    /**
     * @var FormPageAdminListConfigurator
     */
    protected $object;

    protected function setUp(): void
    {
        $em = $this->getMockedEntityManager();
        $aclHelper = $this->createMock('Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper');

        $this->object = new FormPageAdminListConfigurator($em, $aclHelper, self::PERMISSION_VIEW);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getQuoteStrategy')->willReturn(null);

        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->willReturn(null);
        $repository->method('findBy')->willReturn(null);
        $repository->method('findOneBy')->willReturn(null);

        $emMock = $this->createMock(EntityManager::class);
        $emMock->method('getRepository')->willReturn($repository);
        $emMock->method('getClassMetaData')->willReturn((object) ['name' => 'aClass']);
        $emMock->method('getConfiguration')->willReturn($configuration);
        $emMock->method('persist')->willReturn(null);
        $emMock->method('flush')->willReturn(null);

        return $emMock;
    }

    public function testAdaptQueryBuilder()
    {
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilder->expects($this->once())
            ->method('innerJoin')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->once())
            ->method('andWhere')
            ->will($this->returnSelf());

        /* @var QueryBuilder $queryBuilder */
        $this->object->adaptQueryBuilder($queryBuilder);
    }

    public function testFixedGetters()
    {
        $item = $this->createMock(AbstractPage::class);
        $item->method('getId')->willReturn(123);

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
        $item = $this->createMock(AbstractPage::class);
        $item->method('getId')->willReturn(123);

        $this->object->buildItemActions();
        $actions = $this->object->getItemActions();
        $this->assertCount(1, $actions);
        $this->assertInstanceOf(SimpleItemAction::class, $actions[0]);
        /** @var SimpleItemAction $action */
        $action = $actions[0];
        $this->assertCount(2, $action->getUrlFor($item));
    }
}
