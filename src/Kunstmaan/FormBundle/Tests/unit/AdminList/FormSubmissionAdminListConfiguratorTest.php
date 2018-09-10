<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Codeception\Stub;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\FormBundle\AdminList\FormSubmissionAdminListConfigurator;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

/**
 * This test tests the FormPageAdminListConfigurator
 */
class FormSubmissionAdminListConfiguratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormSubmissionAdminListConfigurator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $em = $this->getMockedEntityManager();
        $node = new Node();
        $node->setId(666);
        $nt = new NodeTranslation();
        $nt->setNode($node);
        $this->object = new FormSubmissionAdminListConfigurator($em, $nt);
    }

    /**
     * https://gist.github.com/1331789
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $repository = Stub::make(EntityRepository::class, [
            'find' => null
        ]);
        $configuration = Stub::make(Configuration::class, [
            'getQuoteStrategy' => null
        ]);
        $emMock  = $this->createMock('\Doctrine\ORM\EntityManager', array('getRepository', 'getConfiguration', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repository));
        $emMock->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($configuration));
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

        $queryBuilder->expects($this->any())
            ->method('setParameter')
            ->will($this->returnSelf());

        /* @var $queryBuilder QueryBuilder */
        $this->object->adaptQueryBuilder($queryBuilder);
    }

    public function testFixedGetters()
    {
        $item = Stub::makeEmpty(AbstractPage::class, ['getId' => 123]);
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
