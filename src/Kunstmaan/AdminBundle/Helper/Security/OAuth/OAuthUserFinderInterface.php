<?php

namespace Kunstmaan\AdminBundle\Helper\Security\OAuth;

/**
 * @deprecated since KunstmaanAdminBundle 5.10 and will be removed in KunstmaanAdminBundle 6.0.
 */
interface OAuthUserFinderInterface
{
    /**
     * Tries to find a user in database based on email and googleId fields.
     * Returns null when nothing has been found.
     *
     * @param string email
     * @param string googleId
     *
     * @return mixed AbstractUser Implementation
     */
    public function findUserByGoogleSignInData($email, $googleId);
}
