<?php

namespace Kunstmaan\DashboardBundle\Helper\Google;

use Exception;
use Google_Client as Client;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ClientHelper
 */
class ClientHelper
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client          $client
     * @param RouterInterface $router
     * @param string          $routeName
     */
    public function __construct(Client $client, RouterInterface $router, $routeName)
    {
        $this->client = $client;

        try {
            $uri = $router->generate($routeName, [], UrlGeneratorInterface::ABSOLUTE_URL);
            $this->client->setRedirectUri($uri);
            $this->client->setAccessType('offline');
            $this->client->setApprovalPrompt('force');
        } catch (Exception $e) {
        }
    }

    /**
     * @return Client $client
     */
    public function getClient()
    {
        return $this->client;
    }
}
