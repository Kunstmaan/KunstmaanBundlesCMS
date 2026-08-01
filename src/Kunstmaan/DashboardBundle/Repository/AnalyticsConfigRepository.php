<?php

namespace Kunstmaan\DashboardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;

class AnalyticsConfigRepository extends EntityRepository
{
    /**
     * Get the first config from the database, creates a new entry if the config doesn't exist yet
     *
     * @return AnalyticsConfig|false
     */
    public function findFirst($createNew = true)
    {
        // Backwards compatibility: select the first config, still used in the dashboard, specified config ids are set in the dashboard collection bundle
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM Kunstmaan\DashboardBundle\Entity\AnalyticsConfig c');
        $result = $query->getResult();
        // if no configs exist, create a new one
        if (!$result && $createNew) {
            return $this->createConfig();
        }

        if ($result) {
            return $result[0];
        }

        return false;
    }

    /**
     * Get the default overviews for a config
     *
     * @return AnalyticsConfig
     */
    public function findDefaultOverviews($config)
    {
        $em = $this->getEntityManager();

        return $em->getRepository(AnalyticsOverview::class)
            ->findBy([
                'config' => $config,
                'segment' => null,
            ]);
    }

    /**
     * @return AnalyticsConfig
     */
    public function createConfig()
    {
        $em = $this->getEntityManager();

        $config = new AnalyticsConfig();
        $em->persist($config);
        $em->flush();

        $this->getEntityManager()->getRepository(AnalyticsOverview::class)->addOverviews($config);

        return $config;
    }

    /**
     * @param int|false $id the config id
     */
    public function flushConfig($id = false)
    {
        $em = $this->getEntityManager();

        // Backward compatibilty to flush overviews without a config set
        if (!$id) {
            $overviewRepository = $em->getRepository(AnalyticsOverview::class);
            foreach ($overviewRepository->findAll() as $overview) {
                $em->remove($overview);
            }
            $em->flush();

            return;
        }

        $config = $id ? $this->find($id) : $this->findFirst();
        foreach ($config->getOverviews() as $overview) {
            $em->remove($overview);
        }
        foreach ($config->getSegments() as $segment) {
            $em->remove($segment);
        }
        $em->flush();
    }

    /**
     * Update the timestamp when data is collected
     *
     * @param int|false $id
     */
    public function setUpdated($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setLastUpdate(new \DateTime());
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string    $token
     * @param int|false $id
     */
    public function saveToken($token, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setToken($token);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string    $propertyId
     * @param int|false $id
     */
    public function savePropertyId($propertyId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setPropertyId($propertyId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * saves the account id
     *
     * @param string    $accountId
     * @param int|false $id
     */
    public function saveAccountId($accountId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setAccountId($accountId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string    $profileId
     * @param int|false $id
     */
    public function saveProfileId($profileId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setProfileId($profileId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string    $name
     * @param int|false $id
     */
    public function saveConfigName($name, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setName($name);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param int|false $id
     */
    public function resetProfileId($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setProfileId('');
        $em->persist($config);
        $em->flush();
    }

    /**
     * Resets the  account id, property id and profile id
     *
     * @param int|false $id
     */
    public function resetPropertyId($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        if (false === $config) {
            throw new \RuntimeException('No analytics config found.');
        }
        $config->setAccountId('');
        $config->setProfileId('');
        $config->setPropertyId('');
        $em->persist($config);
        $em->flush();
    }
}
