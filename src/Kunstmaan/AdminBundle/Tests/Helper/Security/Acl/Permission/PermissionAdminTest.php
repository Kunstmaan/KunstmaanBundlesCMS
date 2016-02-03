<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

class PermissionAdminTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PermissionAdmin $object
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::initialize
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::getPermissions
     */
    public function testInitialize()
    {
        $object = $this->getInitializedPermissionAdmin();

        $this->assertEquals(array('ROLE_TEST' => new MaskBuilder(1)), $object->getPermissions());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::initialize
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::getPermission
     */
    public function testGetPermissionWithString()
    {
        $object = $this->getInitializedPermissionAdmin();

        $this->assertEquals(new MaskBuilder(1), $object->getPermission('ROLE_TEST'));
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::initialize
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::getPermission
     */
    public function testGetPermissionWithRoleObject()
    {
        $object = $this->getInitializedPermissionAdmin();

        $role = $this->getMock('Symfony\Component\Security\Core\Role\RoleInterface');
        $role->expects($this->once())
            ->method('getRole')
            ->will($this->returnValue('ROLE_TEST'));
        $this->assertEquals(new MaskBuilder(1), $object->getPermission($role));
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::initialize
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::getPermission
     */
    public function testGetPermissionWithUnknownRole()
    {
        $object = $this->getInitializedPermissionAdmin();

        $this->assertNull($object->getPermission('ROLE_UNKNOWN'));
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::__construct
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::getAllRoles
     */
    public function testGetAllRoles()
    {
        $roleRepo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $roleRepo->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(null));

        $em = $this->getEntityManager();
        $em->expects($this->once())
            ->method('getRepository')
            ->with('KunstmaanAdminBundle:Role')
            ->will($this->returnValue($roleRepo));
        $context = $this->getTokenStorage();
        $aclProvider = $this->getAclProvider();
        $retrievalStrategy = $this->getOidRetrievalStrategy();
        $dispatcher = $this->getEventDispatcher();
        $shell = $this->getShell();
        $kernel = $this->getKernel();
        $object = new PermissionAdmin($em, $context, $aclProvider, $retrievalStrategy, $dispatcher, $shell, $kernel);

        $this->assertNull($object->getAllRoles());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::__construct
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::getPossiblePermissions
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::initialize
     */
    public function testGetPossiblePermissions()
    {
        $em = $this->getEntityManager();
        $context = $this->getTokenStorage();
        $aclProvider = $this->getAclProvider();
        $retrievalStrategy = $this->getOidRetrievalStrategy();
        $retrievalStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->throwException(new \Symfony\Component\Security\Acl\Exception\AclNotFoundException()));
        $dispatcher = $this->getEventDispatcher();
        $shell = $this->getShell();
        $kernel = $this->getKernel();
        $object = new PermissionAdmin($em, $context, $aclProvider, $retrievalStrategy, $dispatcher, $shell, $kernel);

        $permissions = array('PERMISSION1', 'PERMISSION2');
        $permissionMap = $this->getMock('Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface');
        $permissionMap
            ->expects($this->any())
            ->method('getPossiblePermissions')
            ->will($this->returnValue($permissions));
        $entity = $this->getEntity();
        /* @var $permissionMap PermissionMapInterface */
        $object->initialize($entity, $permissionMap);
        $this->assertEquals($permissions, $object->getPossiblePermissions());
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin::createAclChangeset
     */
    public function testCreateAclChangeset()
    {
        $em = $this->getEntityManager();
        $em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf('Kunstmaan\AdminBundle\Entity\AclChangeset'));
        $em->expects($this->once())
            ->method('flush');
        $context = $this->getTokenStorage();
        $aclProvider = $this->getAclProvider();
        $retrievalStrategy = $this->getOidRetrievalStrategy();
        $dispatcher = $this->getEventDispatcher();
        $shell = $this->getShell();
        $kernel = $this->getKernel();
        $object = new PermissionAdmin($em, $context, $aclProvider, $retrievalStrategy, $dispatcher, $shell, $kernel);

        $entity = $this->getEntity();
        /* @var $user User */
        $user = $this->getMockBuilder('Kunstmaan\AdminBundle\Entity\User')
            ->disableOriginalConstructor()
            ->getMock();

        $object->createAclChangeSet($entity, array(), $user);
    }

    /**
     * Return entity manager mock
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Return alc provider mock
     *
     * @return AclProviderInterface
     */
    public function getAclProvider()
    {
        return $this->getMock('Symfony\Component\Security\Acl\Model\AclProviderInterface');
    }

    /**
     * Return security token storage
     *
     * @return TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
    }

    /**
     * Return oid retrieval strategy mock
     *
     * @return ObjectIdentityRetrievalStrategyInterface
     */
    public function getOidRetrievalStrategy()
    {
        return $this->getMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
    }

    /**
     * Return event dispatcher mock
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->getMock('Symfony\Component\EventDispatcher\EventDispatcher');
    }

    /**
     * @return Shell
     */
    public function getShell()
    {
        return new Shell();
    }

    /**
     * @return KernelInterface
     */
    public function getKernel()
    {
        return $this->getMock('Symfony\Component\HttpKernel\KernelInterface');
    }

    /**
     * Return permission admin mock
     *
     * @return PermissionAdmin
     */
    public function getPermissionAdmin()
    {
        $em = $this->getEntityManager();
        $context = $this->getTokenStorage();

        $securityIdentity = new RoleSecurityIdentity('ROLE_TEST');

        $entity = $this->getMockBuilder('Symfony\Component\Security\Acl\Domain\Entry')
            ->disableOriginalConstructor()
            ->getMock();
        $entity
            ->expects($this->any())
            ->method('getSecurityIdentity')
            ->will($this->returnValue($securityIdentity));
        $entity
            ->expects($this->any())
            ->method('getMask')
            ->will($this->returnValue(1));

        $acl = $this->getMockBuilder('Symfony\Component\Security\Acl\Domain\Acl')
            ->disableOriginalConstructor()
            ->getMock();
        $acl->expects($this->once())
            ->method('getObjectAces')
            ->will($this->returnValue(array($entity)));

        $aclProvider = $this->getAclProvider();
        $aclProvider
            ->expects($this->once())
            ->method('findAcl')
            ->with($this->anything())
            ->will($this->returnValue($acl));

        $retrievalStrategy = $this->getOidRetrievalStrategy();
        $objectIdentity = $this->getMock('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface');
        $retrievalStrategy
            ->expects($this->once())
            ->method('getObjectIdentity')
            ->will($this->returnValue($objectIdentity));
        $dispatcher = $this->getEventDispatcher();
        $shell = $this->getShell();
        $kernel = $this->getKernel();
        $object = new PermissionAdmin($em, $context, $aclProvider, $retrievalStrategy, $dispatcher, $shell, $kernel);

        return $object;
    }

    /**
     * Return entity mock
     *
     * @return AbstractEntity
     */
    public function getEntity()
    {
        return $this->getMockForAbstractClass('Kunstmaan\AdminBundle\Entity\AbstractEntity');
    }

    /**
     * Return permission admin mock
     *
     * @return PermissionAdmin
     */
    public function getInitializedPermissionAdmin()
    {
        $object = $this->getPermissionAdmin();
        $entity = $this->getEntity();
        /* @var $permissionMap PermissionMapInterface */
        $permissionMap = $this->getMock('Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface');
        $object->initialize($entity, $permissionMap);

        return $object;
    }
}
