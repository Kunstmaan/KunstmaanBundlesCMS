<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Codeception\Stub;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use ReflectionClass;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\AuditableEntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PermissionAdminTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PermissionAdmin
     */
    protected $object;

    public function testInitialize()
    {
        $object = $this->getInitializedPermissionAdmin();
        $this->assertEquals(array('ROLE_TEST' => new MaskBuilder(1)), $object->getPermissions());
    }

    public function testGetPermissionWithString()
    {
        $object = $this->getInitializedPermissionAdmin();
        $this->assertEquals(new MaskBuilder(1), $object->getPermission('ROLE_TEST'));
    }

    public function testGetPermissionWithRoleObject()
    {
        $object = $this->getInitializedPermissionAdmin();

        $role = $this->createMock('Symfony\Component\Security\Core\Role\RoleInterface');
        $role->expects($this->once())
            ->method('getRole')
            ->will($this->returnValue('ROLE_TEST'));
        $this->assertEquals(new MaskBuilder(1), $object->getPermission($role));
    }

    public function testGetPermissionWithUnknownRole()
    {
        $object = $this->getInitializedPermissionAdmin();
        $this->assertNull($object->getPermission('ROLE_UNKNOWN'));
    }

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
        $permissionMap = $this->createMock('Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface');
        $permissionMap
            ->expects($this->any())
            ->method('getPossiblePermissions')
            ->will($this->returnValue($permissions));
        $entity = $this->getEntity();
        /* @var $permissionMap PermissionMapInterface */
        $object->initialize($entity, $permissionMap);
        $this->assertEquals($permissions, $object->getPossiblePermissions());
    }

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
        return $this->createMock('Symfony\Component\Security\Acl\Model\MutableAclProviderInterface');
    }

    /**
     * Return security token storage
     *
     * @return TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->createMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
    }

    /**
     * Return oid retrieval strategy mock
     *
     * @return ObjectIdentityRetrievalStrategyInterface
     */
    public function getOidRetrievalStrategy()
    {
        return $this->createMock('Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface');
    }

    /**
     * Return event dispatcher mock
     *
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->createMock('Symfony\Component\EventDispatcher\EventDispatcher');
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
        return $this->createMock('Symfony\Component\HttpKernel\KernelInterface');
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
        $acl->expects($this->atLeastOnce())
            ->method('getObjectAces')
            ->will($this->returnValue(array($entity)));

        $aclProvider = $this->getAclProvider();
        $aclProvider
            ->expects($this->atLeastOnce())
            ->method('findAcl')
            ->with($this->anything())
            ->will($this->returnValue($acl));

        $retrievalStrategy = $this->getOidRetrievalStrategy();
        $objectIdentity = $this->createMock('Symfony\Component\Security\Acl\Model\ObjectIdentityInterface');
        $retrievalStrategy
            ->expects($this->atLeastOnce())
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
        $permissionMap = $this->createMock('Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface');
        $object->initialize($entity, $permissionMap);

        return $object;
    }

    public function testBindRequestReturnsTrueWhenNoChanges()
    {
        $object = $this->getInitializedPermissionAdmin();
        $request = $this->createMock(Request::class);
        $request->request = $this->createMock(Request::class);
        $request->request->expects($this->once())->method('get')->willReturn('');
        $object->bindRequest($request);
    }

    /**
     * @throws \ReflectionException
     */
    public function testBindRequest()
    {
        $object = $this->getInitializedPermissionAdmin();
        $token = $this->createMock(PreAuthenticatedToken::class);
        $token->expects($this->once())->method('getUser')->willReturn(new User());
        $request = $this->createMock(Request::class);
        $request->request = $this->createMock(Request::class);
        $request->request->expects($this->any())->method('get')->will($this->onConsecutiveCalls(['ADMIN' => ['ADD' => ['VIEW']]], true));

        $mirror = new ReflectionClass(PermissionAdmin::class);
        $property = $mirror->getProperty('tokenStorage');
        $property->setAccessible(true);
        $val = $property->getValue($object);
        $val->expects($this->once())->method('getToken')->willReturn($token);

        $object->bindRequest($request);
    }

    /**
     * @throws \ReflectionException
     */
    public function testMaskAtIndex()
    {
        $object = $this->getInitializedPermissionAdmin();

        $id = $this->createMock(SecurityIdentityInterface::class);
        $ace = $this->createMock(AuditableEntryInterface::class);
        $ace->expects($this->once())->method('getSecurityIdentity')->willReturn($id);
        $acl = $this->createMock(AclInterface::class);
        $acl->expects($this->once())->method('getObjectAces')->willReturn([1 => $ace]);

        $mirror = new ReflectionClass(PermissionAdmin::class);
        $method = $mirror->getMethod('getMaskAtIndex');
        $method->setAccessible(true);

        $this->assertFalse($method->invoke($object, $acl, 1));

        $id = new RoleSecurityIdentity('ADMIN');
        $ace = $this->createMock(AuditableEntryInterface::class);
        $ace->expects($this->once())->method('getSecurityIdentity')->willReturn($id);
        $ace->expects($this->once())->method('getMask')->willReturn(true);
        $acl = $this->createMock(AclInterface::class);
        $acl->expects($this->once())->method('getObjectAces')->willReturn([1 => $ace]);

        $this->assertTrue($method->invoke($object, $acl, 1));
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetManageableRolesForPages()
    {
        $object = $this->getInitializedPermissionAdmin();

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->once())->method('findAll')->willReturn(['ROLE_SUPER_ADMIN', 'USER']);

        $em = $this->getEntityManager();
        $em->expects($this->once())->method('getRepository')->willReturn($repo);

        $token = $this->createMock(PreAuthenticatedToken::class);
        $token->expects($this->once())->method('getUser')->willReturn(new User());

        $storage = $this->createMock(TokenStorage::class);
        $storage->expects($this->once())->method('getToken')->willReturn($token);

        $mirror = new ReflectionClass(PermissionAdmin::class);
        $property = $mirror->getProperty('tokenStorage');
        $property->setAccessible(true);
        $property->setValue($object, $storage);
        $property = $mirror->getProperty('em');
        $property->setAccessible(true);
        $property->setValue($object, $em);

        $roles = $object->getManageableRolesForPages();
        $this->assertCount(1, $roles);
        $this->assertTrue(in_array('USER', $roles));
    }

    /**
     * @throws \ReflectionException
     */
    public function testApplyAclChangesetReturnsNull()
    {
        $object = $this->getInitializedPermissionAdmin();
        /** @var AbstractEntity $entity */
        $entity = Stub::makeEmpty(AbstractEntity::class, ['getId' => 666, 'setId' => null, '__toString' => '666']);
        $this->assertNull($object->applyAclChangeset($entity, [], true));
    }

    /**
     * @throws \ReflectionException
     */
    public function testApplyAclAppliesChangesetRecursive()
    {
        $object = $this->getInitializedPermissionAdmin();
        $acl = $this->createMock(MutableAclInterface::class);
        $provider = $this->createMock(MutableAclProviderInterface::class);
        $provider->expects($this->atLeastOnce())->method('findAcl')->willThrowException(new AclNotFoundException());
        $provider->expects($this->atLeastOnce())->method('createAcl')->willReturn($acl);

        $mirror = new ReflectionClass(PermissionAdmin::class);
        $property = $mirror->getProperty('aclProvider');
        $property->setAccessible(true);
        $property->setValue($object, $provider);

        $entity = new Node();
        $entity->setChildren(new ArrayCollection([new Node()]));
        $this->assertNull($object->applyAclChangeset($entity, [], true));
    }

    /**
     * @throws \ReflectionException
     */
    public function testApplyAclAppliesChangeset()
    {
        $object = $this->getInitializedPermissionAdmin();

        $entity = new Node();
        $this->assertNull($object->applyAclChangeset($entity, ['ROLE_TEST' => ['DEL' => ['VIEW']]], false));
    }
}
