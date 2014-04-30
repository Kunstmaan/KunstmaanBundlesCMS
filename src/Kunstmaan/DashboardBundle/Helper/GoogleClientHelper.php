<?php

namespace Kunstmaan\DashboardBundle\Helper;

use Doctrine\ORM\EntityManager;
use Google_Client;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Symfony\Cmf\Component\Routing\ChainRouter;

/**
 * This helper will setup a google api client object
 */
class GoogleClientHelper
{
    /** @var string $token */
    private $token = false;

    /** @var string $accountId */
    private $propertyId = false;

    /** @var string $accountId */
    private $accountId = false;

    /** @var string $profileId */
    private $profileId = false;

    /** @var Google_Client $client */
    private $client;

    /** @var EntityManager $em */
    private $em;


    /**
     * Constructor
     *
     * @param $googleClient
     * @param EntityManager $em
     */
    public function __construct($googleClient, $em)
    {
        $this->client = $googleClient;
        $this->em = $em;

        $this->init();
    }

    /**
     * Tries to initialise the Client object
     *
     * @throws \Exception when API parameters are not set or incomplete
     */
    public function init($configId=false)
    {
        // if token is already saved in the database
        if ($this->getToken($configId) && '' !== $this->getToken($configId)) {
            $this->client->setAccessToken($this->token);
        }
    }

    /**
     * sets the redirect URI of the API client
     *
     * @param ChainRouter $router
     * @param string $routeName
     */
    public function setRedirectUri(ChainRouter $router, $routeName)
    {
        try {
            $uri = $router->generate($routeName, array(), true);
            $this->client->setRedirectUri($uri);
        } catch (\Exception $e) {
            $this->client->setRedirectUri('');
        }
    }

    /**
     * Get the token from the database
     *
     * @return string $token
     */
    private function getToken($configId=false)
    {
        if (!$this->token || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            $this->token = $analyticsConfigRepository->getConfig($configId)->getToken();
        }

        return $this->token;
    }

    /**
     * Get the accountId from the database
     *
     * @return string $accountId
     */
    public function getAccountId($configId=false)
    {
        if (!$this->accountId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            $this->accountId = $analyticsConfigRepository->getConfig($configId)->getAccountId();
        }

        return $this->accountId;
    }

    /**
     * Get the propertyId from the database
     *
     * @return string $propertyId
     */
    public function getPropertyId($configId=false)
    {
        if (!$this->propertyId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            $this->propertyId = $analyticsConfigRepository->getConfig($configId)->getPropertyId();
        }

        return $this->propertyId;
    }

    /**
     * Get the propertyId from the database
     *
     * @return string $propertyId
     */
    public function getProfileId($configId=false)
    {
        if (!$this->profileId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            $this->profileId = $analyticsConfigRepository->getConfig($configId)->getProfileId();
        }

        return $this->profileId;
    }

    /**
     * Save the token to the database
     */
    public function saveToken($token, $configId=false)
    {
        $this->token = $token;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveToken($token, $configId);
    }

    /**
     * Save the accountId to the database
     */
    public function saveAccountId($accountId, $configId=false)
    {
        $this->accountId = $accountId;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveAccountId($accountId, $configId);
    }

    /**
     * Save the propertyId to the database
     */
    public function savePropertyId($propertyId, $configId=false)
    {
        $this->propertyId = $propertyId;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->savePropertyId($propertyId, $configId);
    }

    /**
     * Save the profileId to the database
     */
    public function saveProfileId($profileId, $configId=false)
    {
        $this->profileId = $profileId;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveProfileId($profileId, $configId);
    }

    /**
     * Save the config to the database
     */
    public function saveConfigName($configName, $configId=false)
    {
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveConfigName($configName, $configId);
    }

    /**
     * Get the client
     *
     * @return Google_Client $client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Check if token is set
     *
     * @return boolean $result
     */
    public function tokenIsSet()
    {
        return $this->getToken() && '' !== $this->getToken();
    }


    /**
     * Check if token is set
     *
     * @return boolean $result
     */
    public function accountIsSet()
    {
        return $this->getAccountId() && '' !== $this->getAccountId();
    }

    /**
     * Check if propertyId is set
     *
     * @return boolean $result
     */
    public function propertyIsSet()
    {
        return null !== $this->getPropertyId() && '' !== $this->getPropertyId();
    }

    /**
     * Check if token is set
     *
     * @return boolean $result
     */
    public function profileIsSet()
    {
        return null !== $this->getProfileId() && '' !== $this->getProfileId();
    }

}
