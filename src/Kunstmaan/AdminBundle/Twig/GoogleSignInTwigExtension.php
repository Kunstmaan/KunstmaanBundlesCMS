<?php

namespace Kunstmaan\AdminBundle\Twig;

use Twig_Extension;

/**
 * Class GoogleSignInTwigExtension
 */
class GoogleSignInTwigExtension extends Twig_Extension
{
    private $enabled;

    private $clientId;

    public function __construct($enabled, $clientId)
    {
        $this->enabled = $enabled;
        $this->clientId = $clientId;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('google_signin_enabled', array($this, 'isGoogleSignInEnabled')),
            new \Twig_SimpleFunction('google_signin_client_id', array($this, 'getClientId')),
        );
    }

    public function isGoogleSignInEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }
}
