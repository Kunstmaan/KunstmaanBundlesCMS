<?php

namespace Kunstmaan\DashboardBundle\Helper\Google\Analytics;

use Doctrine\ORM\EntityManager;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;

/**
 * Class ConfigHelper
 */
class ConfigHelper
{
    /** @var ServiceHelper */
    private $serviceHelper;

    /** @var string $token */
    private $token = false;

    /** @var string $accountId */
    private $propertyId = false;

    /** @var string $accountId */
    private $accountId = false;

    /** @var string $profileId */
    private $profileId = false;

    /** @var EntityManager $em */
    private $em;

    /**
     * ConfigHelper constructor.
     *
     * @param ServiceHelper $serviceHelper
     * @param EntityManager $em
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function __construct(ServiceHelper $serviceHelper, EntityManager $em)
    {
        $this->serviceHelper = $serviceHelper;
        $this->em = $em;
        $this->init();
    }

    /**
     * @param bool $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function init($configId = false)
    {
        // if token is already saved in the database
        if ($this->getToken($configId) && '' !== $this->getToken($configId)) {
            $client = $this
                ->serviceHelper
                ->getClientHelper()
                ->getClient();

            $client->setAccessToken($this->getToken($configId));

            if ($client->isAccessTokenExpired()) {
                $client->fetchAccessTokenWithRefreshToken();
                $this->saveToken(json_encode($client->getAccessToken()));
            }
        }

        if ($configId) {
            $this->getAccountId($configId);
            $this->getPropertyId($configId);
            $this->getProfileId($configId);
        }
    }

    /* =============================== TOKEN =============================== */

    /**
     * @param bool $configId
     *
     * @return string
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getToken($configId = false)
    {
        if (!$this->token || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            if ($configId) {
                $this->token = $analyticsConfigRepository->find($configId)->getToken();
            } else {
                $this->token = $analyticsConfigRepository->findFirst()->getToken();
            }
        }

        return $this->token;
    }

    /**
     * @param string $token
     * @param bool   $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveToken($token, $configId = false)
    {
        $this->token = $token;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveToken($token, $configId);
    }

    /**
     * @return bool
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function tokenIsSet()
    {
        return $this->getToken() && '' !== $this->getToken();
    }

    /* =============================== ACCOUNT =============================== */

    /**
     * Get a list of all available accounts
     *
     * @return array $data A list of all properties
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
     * @param bool $configId
     *
     * @return string
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getAccountId($configId = false)
    {
        if (!$this->accountId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            if ($configId) {
                $this->accountId = $analyticsConfigRepository->find($configId)->getAccountId();
            } else {
                $this->accountId = $analyticsConfigRepository->findFirst()->getAccountId();
            }
        }

        return $this->accountId;
    }

    /**
     * @param string $accountId
     * @param bool   $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveAccountId($accountId, $configId = false)
    {
        $this->accountId = $accountId;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveAccountId($accountId, $configId);
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function accountIsSet()
    {
        return $this->getAccountId() && '' !== $this->getAccountId();
    }

    /* =============================== PROPERTY =============================== */

    /**
     * @param bool $accountId
     *
     * @return array|bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getProperties($accountId = false)
    {
        if (!$accountId && !$this->getAccountId()) {
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
                    'propertyName' => $property->getName().' ('.$property->getWebsiteUrl().')',
                ];
            }
        }
        ksort($data);

        return $data;
    }

    /**
     * @param bool $configId
     *
     * @return string
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getPropertyId($configId = false)
    {
        if (!$this->propertyId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            if ($configId) {
                $this->propertyId = $analyticsConfigRepository->find($configId)->getPropertyId();
            } else {
                $this->propertyId = $analyticsConfigRepository->findFirst()->getPropertyId();
            }
        }

        return $this->propertyId;
    }

    /**
     * @param      $propertyId
     * @param bool $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function savePropertyId($propertyId, $configId = false)
    {
        $this->propertyId = $propertyId;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->savePropertyId($propertyId, $configId);
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function propertyIsSet()
    {
        return null !== $this->getPropertyId() && '' !== $this->getPropertyId();
    }

    /* =============================== PROFILE =============================== */

    /**
     * @param bool $accountId
     * @param bool $propertyId
     *
     * @return array|bool
     * @throws \Doctrine\ORM\OptimisticLockException
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
     * @param bool $configId
     *
     * @return string
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getProfileId($configId = false)
    {
        if (!$this->profileId || $configId) {
            /** @var AnalyticsConfigRepository $analyticsConfigRepository */
            $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
            if ($configId) {
                $this->profileId = $analyticsConfigRepository->find($configId)->getProfileId();
            } else {
                $this->profileId = $analyticsConfigRepository->findFirst()->getProfileId();
            }
        }

        return $this->profileId;
    }

    /**
     * @param      $profileId
     * @param bool $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveProfileId($profileId, $configId = false)
    {
        $this->profileId = $profileId;
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveProfileId($profileId, $configId);
    }

    /**
     * @return bool
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function profileIsSet()
    {
        return null !== $this->getProfileId() && '' !== $this->getProfileId();
    }

    /**
     * Get the active profile
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getActiveProfile()
    {
        $profiles = $this->getProfiles();
        $profileId = $this->getProfileId();

        if (!\is_array($profiles)) {
            throw new \Exception('<fg=red>The config is invalid</fg=red>');
        }

        foreach ($profiles as $profile) {
            if ($profile['profileId'] === $profileId) {
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
            ->getItems();

        $builtin = [];
        $own = [];
        foreach ($profileSegments as $segment) {
            if ($segment->type === 'BUILT_IN') {
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
     * @param string $configName
     * @param bool   $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveConfigName($configName, $configId = false)
    {
        $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->saveConfigName($configName, $configId);
    }

    /* =============================== AUTH URL =============================== */

    /**
     * get the authUrl
     *
     * @return string $authUrl
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
