<?php

namespace Kunstmaan\DashboardBundle\Helper\Google;
use Google_Client;
use Symfony\Cmf\Component\Routing\ChainRouter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ClientHelper
{

    /** @var Google_Client $client */
    private $client;

    /**
     * Constructor
     *
     * @param Google_Client $client
     * @param ConfigHelper $configHelper
     */
    public function __construct(Google_Client $client, ChainRouter $router, $routeName)
    {
        $this->client = $client;

        try {
            $uri = $router->generate($routeName, array(), UrlGeneratorInterface::ABSOLUTE_URL);
            $this->client->setRedirectUri($uri);
        } catch (\Exception $e) {
            $this->client->setRedirectUri('');
        }
    }

    /**
     * get the client
     *
     * @return Google_Client $client
     */
    public function getClient() {
        return $this->client;
    }

}
