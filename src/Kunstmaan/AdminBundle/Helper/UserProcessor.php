<?php

namespace Kunstmaan\AdminBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Adds the user information to the context of the record which will be logged
 */
class UserProcessor
{
    /** @var ContainerInterface */
    protected $container;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var UserInterface */
    private $user;

    /** @var array */
    private $record = [];

    /**
     * UserProcessor constructor.
     *
     * @param TokenStorageInterface|ContainerInterface $tokenStorage
     */
    public function __construct(/* TokenStorageInterface */ $tokenStorage)
    {
        if ($tokenStorage instanceof ContainerInterface) {
            @trigger_error(
                'Container injection is deprecated in KunstmaanAdminBundle 5.1 and will be removed in KunstmaanAdminBundle 6.0.',
                E_USER_DEPRECATED
            );

            $this->container = $tokenStorage;
            $this->tokenStorage = $tokenStorage->get(TokenStorageInterface::class);

            return;
        }

        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function processRecord(array $record)
    {
        if (null === $this->user) {
            if (($this->tokenStorage !== null) && ($this->tokenStorage->getToken() !== null) && ($this->tokenStorage->getToken()->getUser(
                    ) instanceof AdvancedUserInterface)) {
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
