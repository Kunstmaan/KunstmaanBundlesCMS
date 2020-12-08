<?php

namespace Kunstmaan\DashboardBundle\Helper\Google\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;

class ConfigHelper
{
    /** @var ServiceHelper */
    private $serviceHelper;

    /** @var string */
    private $token = false;

    /** @var string */
    private $propertyId = false;

    /** @var string */
    private $accountId = false;

    /** @var string */
    private $profileId = false;

    /** @var EntityManager */
    private $em;

    public function __construct(ServiceHelper $serviceHelper, EntityManager $em)
    {
        $this->serviceHelper = $serviceHelper;
        $this->em = $em;
        $this->init();
    }

    /**
     * Tries to initialise the Client object
     *
     * @param int|bool $configId
     */
    public function init($configId = false)
    {
        // if token is already saved in the database
        if ($this->getToken($configId) && '' !== $this->getToken($configId)) {
            $this
                ->serviceHelper
                ->getClientHelper()
                ->getClient()
                ->setAccessToken($this->getToken($configId));
        }

        if ($configId) {
            $this->getAccountId($configId);
            $this->getPropertyId($configId);
            $this->getProfileId($configId);
        }
    }

    /* =============================== TOKEN =============================== */

    /**
     * Get the token from the database
     *
     * @return string
     */
    private function getToken($configId = false)
    {
        if (!$this->token || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository(AnalyticsConfig::class);
            if ($configId) {
                $this->token = $analyticsConfigRepository->find($configId)->getToken();
            } else {
                $this->token = $analyticsConfigRepository->findFirst()->getToken();
            }
        }

        return $this->token;
    }

    /**
     * Save the token to the database
     */
    public function saveToken($token, $configId = false)
    {
        $this->token = $token;
        $this->em->getRepository(AnalyticsConfig::class)->saveToken($token, $configId);
    }

    /**
     * Check if token is set
     *
     * @return bool $result
     */
    public function tokenIsSet()
    {
        return $this->getToken() && '' !== $this->getToken();
    }

    /* =============================== ACCOUNT =============================== */

    /**
     * Get a list of all available accounts
     *
     * @return array A list of all properties
     */
    public function getAccounts()
    {
        $accounts = $this->serviceHelper->getService()->management_accounts->listManagementAccounts()->getItems();
        $data = [];

        foreach ($accounts as $account) {
            $data[$account->getName()] = [
                    'accountId' => $account->getId(),
                    'accountName' => $account->getName(),
                ];
        }
        ksort($data);

        return $data;
    }

    /**
     * Get the accountId from the database
     *
     * @return string
     */
    public function getAccountId($configId = false)
    {
        if (!$this->accountId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository(AnalyticsConfig::class);
            if ($configId) {
                $this->accountId = $analyticsConfigRepository->find($configId)->getAccountId();
            } else {
                $this->accountId = $analyticsConfigRepository->findFirst()->getAccountId();
            }
        }

        return $this->accountId;
    }

    /**
     * Save the accountId to the database
     */
    public function saveAccountId($accountId, $configId = false)
    {
        $this->accountId = $accountId;
        $this->em->getRepository(AnalyticsConfig::class)->saveAccountId($accountId, $configId);
    }

    /**
     * Check if token is set
     *
     * @return bool
     */
    public function accountIsSet()
    {
        return $this->getAccountId() && '' !== $this->getAccountId();
    }

    /* =============================== PROPERTY =============================== */

    /**
     * Get a list of all available properties
     *
     * @return array A list of all properties
     */
    public function getProperties($accountId = false)
    {
        if (!$this->getAccountId() && !$accountId) {
            return false;
        }

        if ($accountId) {
            $webproperties = $this->serviceHelper->getService()->management_webproperties->listManagementWebproperties($accountId);
        } else {
            $webproperties = $this->serviceHelper->getService()->management_webproperties->listManagementWebproperties($this->getAccountId());
        }
        $data = [];

        foreach ($webproperties->getItems() as $property) {
            $profiles = $this->getProfiles($accountId, $property->getId());
            if (\count($profiles) > 0) {
                $data[$property->getName()] = [
                        'propertyId' => $property->getId(),
                        'propertyName' => $property->getName() . ' (' . $property->getWebsiteUrl() . ')',
                    ];
            }
        }
        ksort($data);

        return $data;
    }

    /**
     * Get the propertyId from the database
     *
     * @return string
     */
    public function getPropertyId($configId = false)
    {
        if (!$this->propertyId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository(AnalyticsConfig::class);
            if ($configId) {
                $this->propertyId = $analyticsConfigRepository->find($configId)->getPropertyId();
            } else {
                $this->propertyId = $analyticsConfigRepository->findFirst()->getPropertyId();
            }
        }

        return $this->propertyId;
    }

    /**
     * Save the propertyId to the database
     */
    public function savePropertyId($propertyId, $configId = false)
    {
        $this->propertyId = $propertyId;
        $this->em->getRepository(AnalyticsConfig::class)->savePropertyId($propertyId, $configId);
    }

    /**
     * Check if propertyId is set
     *
     * @return bool
     */
    public function propertyIsSet()
    {
        return null !== $this->getPropertyId() && '' !== $this->getPropertyId();
    }

    /* =============================== PROFILE =============================== */

    /**
     * Get a list of all available profiles
     *
     * @return array A list of all properties
     */
    public function getProfiles($accountId = false, $propertyId = false)
    {
        if ((!$this->getAccountId() && !$accountId) || (!$this->getPropertyId() && !$propertyId)) {
            return false;
        }

        // get views
        if ($accountId && $propertyId) {
            $profiles = $this->serviceHelper->getService()->management_profiles->listManagementProfiles(
                    $accountId,
                    $propertyId
                );
        } else {
            $profiles = $this->serviceHelper->getService()->management_profiles->listManagementProfiles(
                    $this->getAccountId(),
                    $this->getPropertyId()
                );
        }

        $data = [];
        if (\is_array($profiles->getItems())) {
            foreach ($profiles->getItems() as $profile) {
                $data[$profile->name] = [
                            'profileId' => $profile->id,
                            'profileName' => $profile->name,
                            'created' => $profile->created,
                        ];
            }
        }
        ksort($data);

        return $data;
    }

    /**
     * Get the propertyId from the database
     *
     * @return string
     */
    public function getProfileId($configId = false)
    {
        if (!$this->profileId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository(AnalyticsConfig::class);
            if ($configId) {
                $this->profileId = $analyticsConfigRepository->find($configId)->getProfileId();
            } else {
                $this->profileId = $analyticsConfigRepository->findFirst()->getProfileId();
            }
        }

        return $this->profileId;
    }

    /**
     * Save the profileId to the database
     */
    public function saveProfileId($profileId, $configId = false)
    {
        $this->profileId = $profileId;
        $this->em->getRepository(AnalyticsConfig::class)->saveProfileId($profileId, $configId);
    }

    /**
     * Check if token is set
     *
     * @return bool
     */
    public function profileIsSet()
    {
        return null !== $this->getProfileId() && '' !== $this->getProfileId();
    }

    /**
     * Get the active profile
     */
    public function getActiveProfile()
    {
        $profiles = $this->getProfiles();
        $profileId = $this->getProfileId();

        if (!\is_array($profiles)) {
            throw new \Exception('<fg=red>The config is invalid</fg=red>');
        }

        foreach ($profiles as $profile) {
            if ($profile['profileId'] == $profileId) {
                return $profile;
            }
        }
    }

    /* =============================== PROFILE SEGMENTS =============================== */

    /**
     * get all segments for the saved profile
     *
     * @return array
     */
    public function getProfileSegments()
    {
        $profileSegments = $this
                    ->serviceHelper
                    ->getService()
                    ->management_segments
                    ->listManagementSegments()
                    ->items;

        $builtin = [];
        $own = [];
        foreach ($profileSegments as $segment) {
            if ($segment->type == 'BUILT_IN') {
                $builtin[] = [
                        'name' => $segment->name,
                        'query' => $segment->segmentId,
                    ];
            } else {
                $own[] = [
                        'name' => $segment->name,
                        'query' => $segment->segmentId,
                    ];
            }
        }

        return ['builtin' => $builtin, 'own' => $own];
    }

    /* =============================== CONFIG =============================== */

    /**
     * Save the config to the database
     */
    public function saveConfigName($configName, $configId = false)
    {
        $this->em->getRepository(AnalyticsConfig::class)->saveConfigName($configName, $configId);
    }

    /* =============================== AUTH URL =============================== */

    /**
     * get the authUrl
     *
     * @return string
     */
    public function getAuthUrl()
    {
        return $this
                ->serviceHelper
                ->getClientHelper()
                ->getClient()
                ->createAuthUrl();
    }
}
