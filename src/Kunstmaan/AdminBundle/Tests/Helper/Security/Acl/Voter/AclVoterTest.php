<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Voter;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Voter\AclVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;

class AclVoterTest extends TestCase
{
    public function testCanConstruct()
    {
        $provider = $this->createMock(AclProviderInterface::class);
        $oid = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $sid = $this->createMock(SecurityIdentityRetrievalStrategyInterface::class);
        $map = $this->createMock(PermissionMapInterface::class);
        $acl = new AclVoter($provider, $oid, $sid, $map);
        $this->assertInstanceOf(AclVoter::class, $acl);
    }
}
