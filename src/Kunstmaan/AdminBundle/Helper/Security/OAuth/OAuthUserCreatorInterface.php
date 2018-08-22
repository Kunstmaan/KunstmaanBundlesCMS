<?php

namespace Kunstmaan\AdminBundle\Helper\Security\OAuth;

/**
 * Interface OAuthUserCreatorInterface
 */
interface OAuthUserCreatorInterface
{
    /**
     * Returns an implementation of AbstractUser defined by the $userClass parameter.
     * Checks if there already exists an account for the given googleId or email. If yes updates
     * the access levels accordingly and returns that user. If no creates a new user with the
     * configured access levels.
     *
     * Returns Null if email is not in configured domains
     *
     * @param string email
     * @param string googleId
     *
     * @return mixed AbstractUser Implementation
    */
    public function getOrCreateUser($email, $googleId);
}
