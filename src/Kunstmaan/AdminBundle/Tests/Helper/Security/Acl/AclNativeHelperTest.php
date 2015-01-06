<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\DBAL\Query\QueryBuilder;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AclNativeHelperTest
 */
class AclNativeHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SecurityContextInterface
     */
    protected $sc;

    /**
     * @var RoleHierarchyInterface
     */
    protected $rh;

    /**
     * @var TokenInterface
     */
    protected $token;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var AclNativeHelper
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->conn = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $this->conn->expects($this->any())
            ->method('getDatabase')
            ->will($this->returnValue('myDatabase'));

        /* @var $platform AbstractPlatform */
        $platform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');

        $this->conn->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($platform));

        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($this->conn));

        /* @var $meta ClassMetadata */
        $meta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->sc = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->getMock();

        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->getMock();

        $this->sc->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->token));

        $this->rh = $this->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchyInterface')
            ->getMock();

        $this->object = new AclNativeHelper($this->em, $this->sc, $this->rh);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper::__construct
     */
    public function testConstructor()
    {
        new AclNativeHelper($this->em, $this->sc, $this->rh);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper::apply
     */
    public function testApply()
    {
        $queryBuilder = new QueryBuilder($this->conn);
        $queryBuilder->add(
            'from',
            array(
                array(
                    'table' => 'myTable',
                    'alias' => 'n'
                )
            )
        );

        $roles = array(new Role('ROLE_KING'));
        $allRoles = array($roles[0], new Role('ROLE_SUBJECT'));

        $this->token->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method('getReachableRoles')
            ->with($roles)
            ->will($this->returnValue($allRoles));

        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')
            ->getMock();

        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('MyUser'));

        $this->token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $permissionDef = new PermissionDefinition(array('view'), 'Kunstmaan\NodeBundle\Entity\Node', 'n');

        /* @var $qb QueryBuilder */
        $qb = $this->object->apply($queryBuilder, $permissionDef);
        $query = $qb->getSQL();

        $this->assertContains('"ROLE_SUBJECT"', $query);
        $this->assertContains('"ROLE_KING"', $query);
        $this->assertContains('"IS_AUTHENTICATED_ANONYMOUSLY"', $query);
        $this->assertContains('MyUser', $query);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper::apply
     */
    public function testApplyAnonymous()
    {
        $queryBuilder = new QueryBuilder($this->conn);
        $queryBuilder->add(
            'from',
            array(
                array(
                    'table' => 'myTable',
                    'alias' => 'n'
                )
            )
        );

        $roles = array();

        $this->token->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method('getReachableRoles')
            ->with($roles)
            ->will($this->returnValue($roles));

        $this->token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue('anon.'));

        $permissionDef = new PermissionDefinition(array('view'), 'Kunstmaan\NodeBundle\Entity\Node', 'n');

        /* @var $qb QueryBuilder */
        $qb = $this->object->apply($queryBuilder, $permissionDef);
        $query = $qb->getSQL();

        $this->assertContains('"IS_AUTHENTICATED_ANONYMOUSLY"', $query);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper::getSecurityContext
     */
    public function testGetSecurityContext()
    {
        $this->assertSame($this->sc, $this->object->getSecurityContext());
    }
}
