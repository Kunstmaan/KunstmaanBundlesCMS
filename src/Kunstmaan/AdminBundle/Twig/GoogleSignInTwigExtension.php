<?php

namespace Kunstmaan\AdminBundle\Twig;

use Twig_Environment;
use Twig_Extension;

/**
 * Class GoogleSignInTwigExtension
 * @package Kunstmaan\AdminBundle\Twig
 */
class GoogleSignInTwigExtension extends Twig_Extension
{
    private $enabled;
    private $clientId;
    private $hostedDomain;

    public function __construct($enabled, $clientId, $hostedDomain)
    {
        $this->enabled = $enabled;
        $this->clientId = $clientId;
        $this->hostedDomain = $hostedDomain;
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
            new \Twig_SimpleFunction('google_signin_hosted_domain', array($this, 'getHostedDomain'))
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

    /**
     * @return mixed
     */
    public function getHostedDomain()
    {
        return $this->hostedDomain;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'google_signin_twig_extension';
    }

}
