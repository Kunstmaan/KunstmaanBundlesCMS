<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit\Framework\TestCase;

class FormSubmissionAdminListConfiguratorTest extends TestCase
{
    /**
     * @var FormSubmissionAdminListConfigurator
     */
    protected $object;

    protected function setUp(): void
    {
        $em = $this->getMockedEntityManager();
        $node = new Node();
        $node->setId(666);
        $nt = new NodeTranslation();
        $nt->setNode($node);
        $this->object = new FormSubmissionAdminListConfigurator($em, $nt);
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

        $queryBuilder->expects($this->atLeastOnce())
            ->method('andWhere')
            ->will($this->returnSelf());

        $queryBuilder->expects($this->atLeastOnce())
            ->method('setParameter')
            ->will($this->returnSelf());

        /* @var QueryBuilder $queryBuilder */
        $this->object->adaptQueryBuilder($queryBuilder);
    }

    public function testFixedGetters()
    {
        $item = $this->createMock(AbstractPage::class);
        $item->method('getId')->willReturn(123);

        $this->assertEquals('', $this->object->getAddUrlFor([]));
        $this->assertEquals('KunstmaanFormBundle', $this->object->getBundleName());
        $this->assertEquals('FormSubmission', $this->object->getEntityName());
        $this->assertCount(0, $this->object->getDeleteUrlFor($item));
        $this->assertCount(2, $this->object->getIndexUrl());
        $this->assertCount(2, $this->object->getEditUrlFor($item));
        $this->assertCount(2, $this->object->getExportUrl());
        $this->assertFalse($this->object->canAdd());
        $this->assertFalse($this->object->canEdit($item));
        $this->assertFalse($this->object->canDelete($item));
        $this->assertTrue($this->object->canExport());
    }

    public function testBuildFilters()
    {
        $this->object->buildFilters();
        $filters = $this->object->getFilterBuilder()->getFilterDefinitions();
        $this->assertCount(3, $filters);
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
