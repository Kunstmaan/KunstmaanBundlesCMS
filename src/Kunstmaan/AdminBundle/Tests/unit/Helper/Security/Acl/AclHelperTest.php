<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Codeception\Stub;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

class AclHelperTest extends \PHPUnit_Framework_TestCase
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

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
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

        /* @var $platform AbstractPlatform */
        $platform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');

        $conn->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue($platform));

        /* @var $stmt Statement */
        $stmt = Stub::makeEmpty(Statement::class);

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
            ->willReturn(array());

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

        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->getMock();

        $this->tokenStorage->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->token));

        $this->rh = $this->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchyInterface')
            ->getMock();

        $this->object = new AclHelper($this->em, $this->tokenStorage, $this->rh);
    }

    public function testConstructor()
    {
        new AclHelper($this->em, $this->tokenStorage, $this->rh);
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
            ->will($this->returnValue(array('Kunstmaan\NodeBundle\Entity\Node')));

        $queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(array('n')));

        $user = $this->getMockBuilder('FOS\UserBundle\Model\UserInterface')
            ->getMock();

        $user->expects($this->any())
            ->method('getUsername')
            ->will($this->returnValue('MyUser'));

        $this->token->expects($this->any())
            ->method('getUser')
            ->will($this->returnValue($user));

        $roles = array(new Role('ROLE_KING'));
        $allRoles = array($roles[0], new Role('ROLE_SUBJECT'));

        $this->token->expects($this->once())
            ->method('getRoles')
            ->will($this->returnValue($roles));

        $this->rh->expects($this->once())
            ->method('getReachableRoles')
            ->with($roles)
            ->will($this->returnValue($allRoles));

        $permissionDef = new PermissionDefinition(array('view'), 'Kunstmaan\NodeBundle\Entity\Node');

        /* @var $query Query */
        $query = $this->object->apply($queryBuilder, $permissionDef);

        $this->assertEquals(MaskBuilder::MASK_VIEW, $query->getHint('acl.mask'));
        $this->assertEquals($permissionDef->getEntity(), $query->getHint('acl.root.entity'));
        $this->assertEquals('rootTable', $query->getHint('acl.entityRootTableName'));
        $this->assertEquals('n', $query->getHint('acl.entityRootTableDqlAlias'));

        $aclQuery = $query->getHint('acl.extra.query');
        $this->assertContains('"ROLE_SUBJECT"', $aclQuery);
        $this->assertContains('"ROLE_KING"', $aclQuery);
        $this->assertContains('"IS_AUTHENTICATED_ANONYMOUSLY"', $aclQuery);
        $this->assertContains('MyUser', $aclQuery);
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
            ->will($this->returnValue(array('Kunstmaan\NodeBundle\Entity\Node')));

        $queryBuilder->expects($this->once())
            ->method('getRootAliases')
            ->will($this->returnValue(array('n')));

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

        $permissionDef = new PermissionDefinition(array('view'), 'Kunstmaan\NodeBundle\Entity\Node');

        /* @var $query Query */
        $query = $this->object->apply($queryBuilder, $permissionDef);

        $this->assertEquals(MaskBuilder::MASK_VIEW, $query->getHint('acl.mask'));
        $this->assertEquals($permissionDef->getEntity(), $query->getHint('acl.root.entity'));
        $this->assertEquals('rootTable', $query->getHint('acl.entityRootTableName'));
        $this->assertEquals('n', $query->getHint('acl.entityRootTableDqlAlias'));

        $aclQuery = $query->getHint('acl.extra.query');
        $this->assertContains('"IS_AUTHENTICATED_ANONYMOUSLY"', $aclQuery);
    }

    public function testGetAllowedEntityIds()
    {
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

        $hydrator = $this->getMockBuilder('Doctrine\ORM\Internal\Hydration\ScalarHydrator')
            ->disableOriginalConstructor()
            ->getMock();

        $rows = array(
            array('id' => 1),
            array('id' => 9),
        );

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

        $permissionDef = new PermissionDefinition(array('view'), 'Kunstmaan\NodeBundle\Entity\Node', 'n');

        /* @var $result array */
        $result = $this->object->getAllowedEntityIds($permissionDef);

        $this->assertEquals(array(1, 9), $result);
    }

    public function testGetAllowedEntityIdsNoEntity()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->object->getAllowedEntityIds(new PermissionDefinition(array('view')));
    }

    public function testGetTokenStorage()
    {
        $this->assertSame($this->tokenStorage, $this->object->getTokenStorage());
    }
}
