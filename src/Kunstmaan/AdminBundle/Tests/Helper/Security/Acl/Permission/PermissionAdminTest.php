<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Entity\AclChangeset;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Acl\Domain\Entry;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\Role;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionAdmin;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\UtilitiesBundle\Helper\Shell\Shell;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
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

class PermissionAdminTest extends TestCase
{
    /**
     * @var PermissionAdmin
     */
    protected $object;

    public function testInitialize()
    {
        $object = $this->getInitializedPermissionAdmin();
        $this->assertEquals(['ROLE_TEST' => new MaskBuilder(1)], $object->getPermissions());
    }

    public function testGetPermissionWithString()
    {
        $object = $this->getInitializedPermissionAdmin();
        $this->assertEquals(new MaskBuilder(1), $object->getPermission('ROLE_TEST'));
    }

    public function testGetPermissionWithRoleObject()
    {
        $object = $this->getInitializedPermissionAdmin();

        $this->assertEquals(new MaskBuilder(1), $object->getPermission(new Role('ROLE_TEST')));
    }

    public function testGetPermissionWithUnknownRole()
    {
        $object = $this->getInitializedPermissionAdmin();
        $this->assertNull($object->getPermission('ROLE_UNKNOWN'));
    }

    public function testGetAllRoles()
    {
        $roleRepo = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $roleRepo->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue(null));

        $em = $this->getEntityManager();
        $em->expects($this->once())
            ->method('getRepository')
            ->with(Role::class)
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
            ->will($this->throwException(new AclNotFoundException()));
        $dispatcher = $this->getEventDispatcher();
        $shell = $this->getShell();
        $kernel = $this->getKernel();
        $object = new PermissionAdmin($em, $context, $aclProvider, $retrievalStrategy, $dispatcher, $shell, $kernel);

        $permissions = ['PERMISSION1', 'PERMISSION2'];
        $permissionMap = $this->createMock(PermissionMapInterface::class);
        $permissionMap
            ->method('getPossiblePermissions')
            ->will($this->returnValue($permissions));
        $entity = $this->getEntity();
        /* @var $permissionMap PermissionMapInterface */
        $object->initialize($entity, $permissionMap);
        $this->assertSame($permissions, $object->getPossiblePermissions());
    }

    public function testCreateAclChangeset()
    {
        $em = $this->getEntityManager();
        $em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(AclChangeset::class));
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
        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->getMock();

        $object->createAclChangeSet($entity, [], $user);
    }

    /**
     * Return entity manager mock
     */
    public function getEntityManager(): EntityManager
    {
        return $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Return alc provider mock
     */
    public function getAclProvider(): AclProviderInterface
    {
        return $this->createMock(MutableAclProviderInterface::class);
    }

    /**
     * Return security token storage
     */
    public function getTokenStorage(): TokenStorageInterface
    {
        return $this->createMock(TokenStorageInterface::class);
    }

    /**
     * Return oid retrieval strategy mock
     */
    public function getOidRetrievalStrategy(): ObjectIdentityRetrievalStrategyInterface
    {
        return $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
    }

    /**
     * Return event dispatcher mock
     */
    public function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->createMock(EventDispatcher::class);
    }

    public function getShell(): Shell
    {
        $mock = $this->createMock(Shell::class);
        $mock->method('runInBackground')
            ->with('php /some/project/directory/bin/console kuma:acl:apply --env=test')
        ;

        return $mock;
    }

    public function getKernel(): KernelInterface
    {
        $mock = $this->createMock(Kernel::class);
        $mock->method('getProjectDir')->willReturn('/some/project/directory');
        $mock->method('getEnvironment')->willReturn('test');

        return $mock;
    }

    /**
     * Return permission admin mock
     */
    public function getPermissionAdmin(): PermissionAdmin
    {
        $em = $this->getEntityManager();
        $context = $this->getTokenStorage();

        $securityIdentity = new RoleSecurityIdentity('ROLE_TEST');

        $entity = $this->getMockBuilder(Entry::class)
            ->disableOriginalConstructor()
            ->getMock();
        $entity
            ->method('getSecurityIdentity')
            ->will($this->returnValue($securityIdentity));
        $entity
            ->method('getMask')
            ->will($this->returnValue(1));

        $acl = $this->getMockBuilder(Acl::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acl->expects($this->atLeastOnce())
            ->method('getObjectAces')
            ->will($this->returnValue([$entity]));

        $aclProvider = $this->getAclProvider();
        $aclProvider
            ->expects($this->atLeastOnce())
            ->method('findAcl')
            ->with($this->anything())
            ->will($this->returnValue($acl));

        $retrievalStrategy = $this->getOidRetrievalStrategy();
        $objectIdentity = $this->createMock(ObjectIdentityInterface::class);
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
     */
    public function getEntity(): AbstractEntity
    {
        return $this->getMockForAbstractClass(AbstractEntity::class);
    }

    /**
     * Return permission admin mock
     */
    public function getInitializedPermissionAdmin(): PermissionAdmin
    {
        $object = $this->getPermissionAdmin();
        $entity = $this->getEntity();
        /* @var $permissionMap PermissionMapInterface */
        $permissionMap = $this->createMock(PermissionMapInterface::class);
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
        $request->request->method('get')->will($this->onConsecutiveCalls(['ADMIN' => ['ADD' => ['VIEW']]], true));

        $mirror = new \ReflectionClass(PermissionAdmin::class);
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

        $mirror = new \ReflectionClass(PermissionAdmin::class);
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

        $mirror = new \ReflectionClass(PermissionAdmin::class);
        $property = $mirror->getProperty('tokenStorage');
        $property->setAccessible(true);
        $property->setValue($object, $storage);
        $property = $mirror->getProperty('em');
        $property->setAccessible(true);
        $property->setValue($object, $em);

        $roles = $object->getManageableRolesForPages();
        $this->assertCount(1, $roles);
        $this->assertTrue(\in_array('USER', $roles));
    }

    /**
     * @throws \ReflectionException
     */
    public function testApplyAclChangesetReturnsNull()
    {
        $object = $this->getInitializedPermissionAdmin();
        $entity = $this->createMock(AbstractEntity::class);
        $entity->method('getId')->willReturn(666);
        $entity->method('setId')->willReturn(null);
        $entity->method('__toString')->willReturn('666');

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

        $mirror = new \ReflectionClass(PermissionAdmin::class);
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
