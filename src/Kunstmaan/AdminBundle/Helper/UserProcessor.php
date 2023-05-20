<?php

namespace Kunstmaan\AdminBundle\Helper;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Monolog\LogRecord;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * NEXT_MAJOR Remove parameter union type when monolog 2 support is removed
     *
     * @return array
     */
    public function processRecord(array|LogRecord $record)
    {
        if (\is_null($this->user)) {
            if (($this->tokenStorage !== null) && ($this->tokenStorage->getToken() !== null) && ($this->tokenStorage->getToken()->getUser() instanceof UserInterface)) {
                $this->user = $this->tokenStorage->getToken()->getUser();
                $this->record['extra']['user']['username'] = method_exists($this->user, 'getUserIdentifier') ? $this->user->getUserIdentifier() : $this->user->getUsername();
                $this->record['extra']['user']['roles'] = $this->user->getRoles();
                $this->record['extra']['user']['is_account_non_locked'] = $this->user->isAccountNonLocked();
                $this->record['extra']['user']['is_enabled'] = $this->user->isEnabled();
            }
        }

        if (isset($this->record['extra']['user'])) {
            if ($record instanceof LogRecord) {
                $record->extra['user'] = $this->record['extra']['user'];
            } else {
                // NEXT_MAJOR Remove when monolog 2 support is removed
                $record = array_merge($record, $this->record);
            }
        }

        return $record;
    }
}
