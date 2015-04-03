<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * AclHelperTest
 */
class AclHelperTest extends \PHPUnit_Framework_TestCase
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
        $stmt = $this->getMockForAbstractClass('Kunstmaan\AdminBundle\Tests\Mocks\StatementMock');

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

        $this->sc = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->getMock();

        $this->token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')
            ->getMock();

        $this->sc->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($this->token));

        $this->rh = $this->getMockBuilder('Symfony\Component\Security\Core\Role\RoleHierarchyInterface')
            ->getMock();

        $this->object = new AclHelper($this->em, $this->sc, $this->rh);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::__construct
     */
    public function testConstructor()
    {
        new AclHelper($this->em, $this->sc, $this->rh);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::apply
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::cloneQuery
     */
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

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::apply
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::cloneQuery
     */
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

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::getAllowedEntityIds
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::getPermittedAclIdsSQLForUser
     */
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
            array('id' => 9)
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

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::getAllowedEntityIds
     */
    public function testGetAllowedEntityIdsNoEntity()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->object->getAllowedEntityIds(new PermissionDefinition(array('view')));
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper::getSecurityContext
     */
    public function testGetSecurityContext()
    {
        $this->assertSame($this->sc, $this->object->getSecurityContext());
    }
}
