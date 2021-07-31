<?php

namespace Kunstmaan\AdminBundle\Helper;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Adds the user information to the context of the record which will be logged
 */
class UserProcessor
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var array
     */
    private $record = [];
    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return array
     */
    public function processRecord(array $record)
    {
        if (\is_null($this->user)) {
            if (($this->tokenStorage !== null) && ($this->tokenStorage->getToken() !== null) && ($this->tokenStorage->getToken()->getUser() instanceof \Symfony\Component\Security\Core\User\AdvancedUserInterface)) {
                $this->user = $this->tokenStorage->getToken()->getUser();
                $this->record['extra']['user']['username'] = $this->user->getUsername();
                $this->record['extra']['user']['roles'] = $this->user->getRoles();
                $this->record['extra']['user']['is_account_non_expired'] = $this->user->isAccountNonExpired();
                $this->record['extra']['user']['is_account_non_locked'] = $this->user->isAccountNonLocked();
                $this->record['extra']['user']['is_credentials_non_expired'] = $this->user->isCredentialsNonExpired();
                $this->record['extra']['user']['is_enabled'] = $this->user->isEnabled();
            }
        }

        return array_merge($record, $this->record);
    }
}
