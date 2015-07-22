<?php

namespace Kunstmaan\AdminBundle\Helper;

use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;

/**
 * adds the user information to the context of the record which will be logged
 */
class UserProcessor
{

    /**
     * Use container else we have a continous loop in our dependency
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var array
     */
    private $record = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array $record
     *
     * @return array
     */
    public function processRecord(array $record)
    {
        if (is_null($this->user)) {
            /* @var SecurityContextInterface $securityContext */
            $securityContext = null;
            try {
                $this->container->get("security.context");
            } catch (ServiceCircularReferenceException $e) {
                //since the securitycontext is deprecated getting the context from the container results in a log line which tries to use this method again....
            }
            if (($securityContext !== null) && ($securityContext->getToken() !== null) && ($securityContext->getToken()->getUser() instanceof \Symfony\Component\Security\Core\User\AdvancedUserInterface)) {
                $this->user = $securityContext->getToken()->getUser();
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
