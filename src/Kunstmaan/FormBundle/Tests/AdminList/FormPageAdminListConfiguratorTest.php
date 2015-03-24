<?php

namespace Kunstmaan\FormBundle\Tests\AdminList;

use Kunstmaan\FormBundle\AdminList\FormPageAdminListConfigurator;
use Kunstmaan\NodeBundle\Tests\Stubs\TestRepository;
use Kunstmaan\FormBundle\Tests\Stubs\TestConfiguration;
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
        $em = $this->getMockedEntityManager();
        $securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        $roleHierarchy = $this->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchyInterface')
          ->getMock();
        $aclHelper = $this->getMock('Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper', array(), array($em, $securityContext, $roleHierarchy));

        $this->object = new FormPageAdminListConfigurator($em, $aclHelper, self::PERMISSION_VIEW);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * https://gist.github.com/1331789
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $emMock  = $this->getMock('\Doctrine\ORM\EntityManager', array('getRepository', 'getConfiguration', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
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

        /* @var $queryBuilder QueryBuilder */
        $this->object->adaptQueryBuilder($queryBuilder);
    }
}
