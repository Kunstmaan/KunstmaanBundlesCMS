<?php

namespace Kunstmaan\AdminBundle\Controller;

class OAuthController
{
    public function checkAction()
    {
        throw new \RuntimeException('You must configure the check path to be handled by the firewall using guard component in your security firewall configuration.');
    }
}
