<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Voter;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Voter\AclVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AclVoterTest extends TestCase
{
    public function testVote()
    {
        [$voter, , $permissionMap] = $this->getVoter();
        $permissionMap
            ->expects($this->atLeastOnce())
            ->method('getMasks')
            ->willReturn(null)
        ;

        $params = [$this->getToken(), null, ['VIEW', 'EDIT', 'DELETE']];
        if (class_exists(Vote::class)) {
            $params[] = new Vote();
        }

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote(...$params));
    }

    public function testVoteWithPermissionsDisabledAndUnsupportedAttribute()
    {
        [$voter] = $this->getVoter(true, false, false);

        $params = [$this->getToken(), null, ['UNSUPPORTED']];
        if (class_exists(Vote::class)) {
            $params[] = new Vote();
        }

        $this->assertSame(VoterInterface::ACCESS_ABSTAIN, $voter->vote(...$params));
    }

    public function testVoteWithPermissionsDisabledAndSupportedAttribute()
    {
        [$voter] = $this->getVoter(true, true, false);

        $params = [$this->getToken(), null, ['VIEW', 'EDIT', 'DELETE']];
        if (class_exists(Vote::class)) {
            $params[] = new Vote();
        }

        $this->assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote(...$params));
    }

    protected function getToken()
    {
        return $this->createMock(TokenInterface::class);
    }

    /**
     * @return array{0: AclVoter, 1: AclProviderInterface, 2: PermissionMapInterface, 3: ObjectIdentityRetrievalStrategyInterface, 4: SecurityIdentityRetrievalStrategyInterface}
     */
    protected function getVoter($allowIfObjectIdentityUnavailable = true, $alwaysContains = true, $permissionsEnabled = true)
    {
        $provider = $this->createMock(AclProviderInterface::class);
        $permissionMap = $this->createMock(PermissionMapInterface::class);
        $oidStrategy = $this->createMock(ObjectIdentityRetrievalStrategyInterface::class);
        $sidStrategy = $this->createMock(SecurityIdentityRetrievalStrategyInterface::class);

        if ($alwaysContains) {
            $permissionMap
                ->expects($this->any())
                ->method('contains')
                ->willReturn(true);
        }

        return [
            new AclVoter($provider, $oidStrategy, $sidStrategy, $permissionMap, null, $allowIfObjectIdentityUnavailable, $permissionsEnabled),
            $provider,
            $permissionMap,
            $oidStrategy,
            $sidStrategy,
        ];
    }
}
