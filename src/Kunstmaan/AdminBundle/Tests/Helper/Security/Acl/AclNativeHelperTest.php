<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Kunstmaan\NodeBundle\Entity\Node;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
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
        $this->em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->conn = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->conn
            ->method('getDatabase')
            ->will($this->returnValue('myDatabase'));

        $this->conn
            ->method('getDatabasePlatform')
            ->willReturn(new MySQL57Platform());

        $this->em
            ->method('getConnection')
            ->will($this->returnValue($this->conn));

        /* @var $meta ClassMetadata */
        $meta = $this->getMockBuilder(ClassMetadata::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMock();

        $this->token = $this->getMockBuilder(AbstractToken::class)
            ->getMock();

        $this->tokenStorage
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

        [$rolesMethodName, $roles, $reachableRolesMethodName, $allRoles] = $this->getRoleMockData();

        $this->token->expects($this->once())
            ->method($rolesMethodName)
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method($reachableRolesMethodName)
            ->with($roles)
            ->will($this->returnValue($allRoles));

        $user = new User();
        $user->setUsername('MyUser');

        $this->token
            ->method('getUser')
            ->will($this->returnValue($user));

        $permissionDef = new PermissionDefinition(['view'], Node::class, 'n');

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

        [$rolesMethodName, $roles, $reachableRolesMethodName, $allRoles] = $this->getRoleMockData(true);

        $this->token->expects($this->once())
            ->method($rolesMethodName)
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method($reachableRolesMethodName)
            ->with($roles)
            ->will($this->returnValue($allRoles));

        $this->token
            ->method('getUser')
            ->will($this->returnValue(method_exists(FirewallConfig::class, 'getAuthenticators') ? null : 'anon.'));

        $permissionDef = new PermissionDefinition(['view'], Node::class, 'n');

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
        $rolesMethodName = 'getRoleNames';
        $reachableRolesMethodName = 'getReachableRoleNames';
        $roles = $anonymous ? [] : ['ROLE_KING'];
        $allRoles = $anonymous ? [] : [$roles[0], 'ROLE_SUBJECT'];

        return [
            $rolesMethodName,
            $roles,
            $reachableRolesMethodName,
            $allRoles,
        ];
    }
}
