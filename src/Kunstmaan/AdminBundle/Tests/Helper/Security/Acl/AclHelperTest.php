<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Model\UserInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class AclHelperTest extends TestCase
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
     * @var AclHelper
     */
    protected $object;

    protected function setUp(): void
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var $conn Connection */
        $conn = $this->getMockBuilder('Doctrine\DBAL\Connection')
            ->disableOriginalConstructor()
            ->getMock();

        $conn->expects($this->any())
            ->method('getDatabase')
            ->will($this->returnValue('myDatabase'));

        $conn->expects($this->any())
            ->method('getDatabasePlatform')
            ->willReturn(new MySqlPlatform());

        /* @var $stmt Statement */
        $stmt = $this->createMock(Statement::class);

        $conn->expects($this->any())
            ->method('executeQuery')
            ->will($this->returnValue($stmt));

        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($conn));

        /* @var $conf Configuration */
        $conf = $this->getMockBuilder('Doctrine\ORM\Configuration')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var $strat QuoteStrategy */
        $strat = $this->getMockBuilder('Doctrine\ORM\Mapping\QuoteStrategy')
            ->disableOriginalConstructor()
            ->getMock();

        $strat->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('rootTable'));

        $conf->expects($this->any())
            ->method('getQuoteStrategy')
            ->will($this->returnValue($strat));

        $conf->expects($this->any())
            ->method('getDefaultQueryHints')
            ->willReturn([]);

        $conf->expects($this->any())
            ->method('isSecondLevelCacheEnabled')
            ->willReturn(false);

        $this->em->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($conf));

        /* @var $meta ClassMetadata */
        $meta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
            ->getMock();

        $this->token = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\AbstractToken');

        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->token));

        $this->rh = $this->getMockBuilder(RoleHierarchy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new AclHelper($this->em, $this->tokenStorage, $this->rh);
    }

    public function testApply()
    {
        /* @var $queryBuilder QueryBuilder */
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $query = new Query($this->em);
        $query->setParameter('paramName', 'paramValue', 'paramType');
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $queryBuilder->expects($this->once())
            ->method('getRootEntities')
            ->will($this->returnValue(['Kunstmaan\NodeBundle\Entity\Node']));

        $queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(['n']));

        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')
            ->getMock();

        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('MyUser'));

        $this->token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        [$rolesMethodName, $roles, $reachableRolesMethodName, $allRoles,] = $this->getRoleMockData();

        $this->token->expects($this->once())
            ->method($rolesMethodName)
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method($reachableRolesMethodName)
            ->with($roles)
            ->will($this->returnValue($allRoles));

        $permissionDef = new PermissionDefinition(['view'], 'Kunstmaan\NodeBundle\Entity\Node');

        /* @var $query Query */
        $query = $this->object->apply($queryBuilder, $permissionDef);

        $this->assertEquals(MaskBuilder::MASK_VIEW, $query->getHint('acl.mask'));
        $this->assertEquals($permissionDef->getEntity(), $query->getHint('acl.root.entity'));
        $this->assertEquals('rootTable', $query->getHint('acl.entityRootTableName'));
        $this->assertEquals('n', $query->getHint('acl.entityRootTableDqlAlias'));

        $aclQuery = $query->getHint('acl.extra.query');
        $this->assertStringContainsString('ROLE_SUBJECT', $aclQuery);
        $this->assertStringContainsString('ROLE_KING', $aclQuery);
        $this->assertStringContainsString('IS_AUTHENTICATED_ANONYMOUSLY', $aclQuery);
        $this->assertStringContainsString('MyUser', $aclQuery);
    }

    public function testApplyAnonymous()
    {
        /* @var $queryBuilder QueryBuilder */
        $queryBuilder = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $query = new Query($this->em);
        $query->setParameter('paramName', 'paramValue', 'paramType');
        $queryBuilder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $queryBuilder->expects($this->once())
            ->method('getRootEntities')
            ->will($this->returnValue(['Kunstmaan\NodeBundle\Entity\Node']));

        $queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(['n']));

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

        $permissionDef = new PermissionDefinition(['view'], 'Kunstmaan\NodeBundle\Entity\Node');

        /* @var $query Query */
        $query = $this->object->apply($queryBuilder, $permissionDef);

        $this->assertEquals(MaskBuilder::MASK_VIEW, $query->getHint('acl.mask'));
        $this->assertEquals($permissionDef->getEntity(), $query->getHint('acl.root.entity'));
        $this->assertEquals('rootTable', $query->getHint('acl.entityRootTableName'));
        $this->assertEquals('n', $query->getHint('acl.entityRootTableDqlAlias'));

        $aclQuery = $query->getHint('acl.extra.query');
        $this->assertStringContainsString('IS_AUTHENTICATED_ANONYMOUSLY', $aclQuery);
    }

    public function testGetAllowedEntityIds()
    {
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

        $hydrator = $this->getMockBuilder('Doctrine\ORM\Internal\Hydration\ScalarHydrator')
            ->disableOriginalConstructor()
            ->getMock();

        $rows = [
            ['id' => 1],
            ['id' => 9],
        ];

        $hydrator->expects($this->once())
            ->method('hydrateAll')
            ->will($this->returnValue($rows));

        $this->em->expects($this->any())
            ->method('newHydrator') // was ->method('getHydrator')
            ->will($this->returnValue($hydrator));

        /* @var $query NativeQuery */
        $query = new NativeQuery($this->em);

        $this->em->expects($this->once())
            ->method('createNativeQuery')
            ->will($this->returnValue($query));

        $permissionDef = new PermissionDefinition(['view'], 'Kunstmaan\NodeBundle\Entity\Node', 'n');

        /* @var $result array */
        $result = $this->object->getAllowedEntityIds($permissionDef);

        $this->assertEquals([1, 9], $result);
    }

    public function testGetAllowedEntityIdsNoEntity()
    {
        $this->expectException('InvalidArgumentException');

        $this->object->getAllowedEntityIds(new PermissionDefinition(['view']));
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
