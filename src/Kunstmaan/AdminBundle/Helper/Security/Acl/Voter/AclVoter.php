<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Voter;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Acl\Voter\AclVoter as BaseAclVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;

if (class_exists(\Symfony\Component\Security\Core\Security::class)) {
    /**
     * Symfony 6 support
     *
     * @internal
     */
    trait AclVoterTrait
    {
        public function vote(TokenInterface $token, $subject, array $attributes): int
        {
            return $this->doVote($token, $subject, $attributes);
        }
    }
} elseif (method_exists(TokenInterface::class, 'eraseCredentials')) {
    /**
     * Symfony 7 support
     *
     * @internal
     */
    trait AclVoterTrait
    {
        public function vote(TokenInterface $token, mixed $subject, array $attributes): int
        {
            return $this->doVote($token, $subject, $attributes);
        }
    }
} else {
    /**
     * Symfony 8 support
     *
     * @internal
     */
    trait AclVoterTrait
    {
        public function vote(TokenInterface $token, mixed $subject, array $attributes, ?Vote $vote = null): int
        {
            return $this->doVote($token, $subject, $attributes, $vote);
        }
    }
}

/**
 * This voter can be used as a base class for implementing your own permissions.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class AclVoter extends BaseAclVoter
{
    use AclVoterTrait;

    /** @var bool */
    private $permissionsEnabled;

    public function __construct(AclProviderInterface $aclProvider, ObjectIdentityRetrievalStrategyInterface $oidRetrievalStrategy, SecurityIdentityRetrievalStrategyInterface $sidRetrievalStrategy, PermissionMapInterface $permissionMap, ?LoggerInterface $logger = null, $allowIfObjectIdentityUnavailable = true, $permissionsEnabled = true)
    {
        parent::__construct($aclProvider, $oidRetrievalStrategy, $sidRetrievalStrategy, $permissionMap, $logger, $allowIfObjectIdentityUnavailable);
        $this->permissionsEnabled = $permissionsEnabled;
    }

    private function doVote(TokenInterface $token, $subject, array $attributes, /* ?Vote */ $vote = null): int
    {
        $attributeIsSupported = false;
        foreach ($attributes as $attribute) {
            if ($this->supportsAttribute($attribute)) {
                $attributeIsSupported = true;

                break;
            }
        }

        if (!$this->permissionsEnabled && $attributeIsSupported) {
            return self::ACCESS_GRANTED;
        }

        if (!$this->permissionsEnabled) {
            return self::ACCESS_ABSTAIN;
        }

        return parent::vote($token, $subject, $attributes, $vote);
    }
}
