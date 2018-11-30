<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Creators;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Creators\ACLPermissionCreator;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;

/**
 * Class ACLPermissionCreatorTest
 */
class ACLPermissionCreatorTest extends PHPUnit_Framework_TestCase
{
    public function testInitByExample()
    {
        $security = new RoleSecurityIdentity('ADMIN');
        $entry = $this->createMock(EntryInterface::class);
        $user = $this->createMock(ObjectIdentityInterface::class);
        $provider = $this->createMock(MutableAclProviderInterface::class);
        $strategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $mutableAcl = $this->createMock(MutableAclInterface::class);

        $entry->expects($this->once())->method('getSecurityIdentity')->willReturn($security);
        $mutableAcl->expects($this->once())->method('getObjectAces')->willReturn([$entry]);
        $strategy->expects($this->exactly(2))->method('getObjectIdentity')->willReturn($user);
        $provider->expects($this->once())->method('findAcl')->willReturn($mutableAcl);
        $provider->expects($this->once())->method('createAcl')->willReturn($mutableAcl);
        $provider->expects($this->once())->method('updateAcl')->willReturn($mutableAcl);

        $aclCreator = new ACLPermissionCreator($provider, $strategy);
        $aclCreator->initByExample($user, new User(), true);
    }

    public function testInitByMap()
    {
        $user = $this->createMock(ObjectIdentityInterface::class);
        $provider = $this->createMock(MutableAclProviderInterface::class);
        $strategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $mutableAcl = $this->createMock(MutableAclInterface::class);

        $strategy->expects($this->once())->method('getObjectIdentity')->willReturn($user);
        $provider->expects($this->once())->method('createAcl')->willReturn($mutableAcl);
        $provider->expects($this->once())->method('updateAcl')->willReturn($mutableAcl);
        $provider->expects($this->once())->method('deleteAcl')->willThrowException(new AclNotFoundException());

        $aclCreator = new ACLPermissionCreator($provider, $strategy);
        $aclCreator->initByMap($user, ['key' => 'value'], true);
    }
}
