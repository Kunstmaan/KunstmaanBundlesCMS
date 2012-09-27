<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;

use Doctrine\ORM\QueryBuilder;

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
        $this->object = new FormPageAdminListConfigurator(self::PERMISSION_VIEW);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator::adaptQueryBuilder
     */
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

        $params = array();

        /* @var $queryBuilder QueryBuilder */
        $this->object->adaptQueryBuilder($queryBuilder, $params);
    }

}
