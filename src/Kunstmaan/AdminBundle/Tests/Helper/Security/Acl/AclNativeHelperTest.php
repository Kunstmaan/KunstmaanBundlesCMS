<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use FOS\UserBundle\Model\UserInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class AclNativeHelperTest extends TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

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

    protected function setUp(): void
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

        $this->conn->expects($this->any())
            ->method('getDatabasePlatform')
            ->willReturn(new MySqlPlatform());

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

        $this->tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
            ->getMock();

        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\AbstractToken')
            ->getMock();

        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->token));

        $this->rh = $this->getMockBuilder(RoleHierarchy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new AclNativeHelper($this->em, $this->tokenStorage, $this->rh);
    }

    public function testApply()
    {
        $queryBuilder = new QueryBuilder($this->conn);
        $queryBuilder->add(
            'from',
            [
                [
                    'table' => 'myTable',
                    'alias' => 'n',
                ],
            ]
        );

        [$rolesMethodName, $roles, $reachableRolesMethodName, $allRoles,] = $this->getRoleMockData();

        $this->token->expects($this->once())
            ->method($rolesMethodName)
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method($reachableRolesMethodName)
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

        $permissionDef = new PermissionDefinition(['view'], 'Kunstmaan\NodeBundle\Entity\Node', 'n');

        /* @var $qb QueryBuilder */
        $qb = $this->object->apply($queryBuilder, $permissionDef);
        $query = $qb->getSQL();

        $this->assertStringContainsString('ROLE_SUBJECT', $query);
        $this->assertStringContainsString('ROLE_KING', $query);
        $this->assertStringContainsString('IS_AUTHENTICATED_ANONYMOUSLY', $query);
        $this->assertStringContainsString('MyUser', $query);
    }

    public function testApplyAnonymous()
    {
        $queryBuilder = new QueryBuilder($this->conn);
        $queryBuilder->add(
            'from',
            [
                [
                    'table' => 'myTable',
                    'alias' => 'n',
                ],
            ]
        );

        [$rolesMethodName, $roles, $reachableRolesMethodName, $allRoles,] = $this->getRoleMockData(true);

        $this->token->expects($this->once())
            ->method($rolesMethodName)
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method($reachableRolesMethodName)
            ->with($roles)
            ->will($this->returnValue($allRoles));

        $this->token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue('anon.'));

        $permissionDef = new PermissionDefinition(['view'], 'Kunstmaan\NodeBundle\Entity\Node', 'n');

        /* @var $qb QueryBuilder */
        $qb = $this->object->apply($queryBuilder, $permissionDef);
        $query = $qb->getSQL();

        $this->assertStringContainsString('IS_AUTHENTICATED_ANONYMOUSLY', $query);
    }

    public function testGetTokenStorage()
    {
        $this->assertSame($this->tokenStorage, $this->object->getTokenStorage());
    }

    private function getRoleMockData($anonymous = false)
    {
        if (Kernel::VERSION_ID >= 40300) {
            $rolesMethodName = 'getRoleNames';
            $reachableRolesMethodName = 'getReachableRoleNames';
            $roles = ['ROLE_KING'];
            $allRoles = [$roles[0], 'ROLE_SUBJECT'];
        } else {
            $rolesMethodName = 'getRoles';
            $reachableRolesMethodName = 'getReachableRoles';
            $roles = $anonymous ? [] : [new Role('ROLE_KING')];
            $allRoles = $anonymous ? [] : [$roles[0], new Role('ROLE_SUBJECT')];
        }

        return [
            $rolesMethodName,
            $roles,
            $reachableRolesMethodName,
            $allRoles,
        ];
    }
}
