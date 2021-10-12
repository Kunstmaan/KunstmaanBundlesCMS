<?php

namespace Kunstmaan\AdminBundle\Controller;

/**
 * @deprecated since KunstmaanAdminBundle 5.10 and will be removed in KunstmaanAdminBundle 6.0.
 */
class OAuthController
{
    /**
     * @deprecated since KunstmaanAdminBundle 5.10 and will be removed in KunstmaanAdminBundle 6.0.
     */
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using guard component in your security firewall configuration.');
    }
}
