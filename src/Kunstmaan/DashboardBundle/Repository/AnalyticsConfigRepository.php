<?php

namespace Kunstmaan\DashboardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;

/**
 * Class AnalyticsConfigRepository
 */
class AnalyticsConfigRepository extends EntityRepository
{
    /**
     * Get the first config from the database, creates a new entry if the config doesn't exist yet
     *
     * @param bool $createNew
     *
     * @return bool|AnalyticsConfig
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function findFirst($createNew = true)
    {
        // Backwards compatibility: select the first config, still used in the dashboard, specified config ids are set in the dashboard collection bundle
        $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT c FROM KunstmaanDashboardBundle:AnalyticsConfig c');
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
     * @return array
     */
    public function findDefaultOverviews($config)
    {
        $em = $this->getEntityManager();

        return $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')
            ->findBy(
                [
                    'config' => $config,
                    'segment' => null,
                ]
            );
    }

    /**
     * @return AnalyticsConfig
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createConfig()
    {
        $em = $this->getEntityManager();

        $config = new AnalyticsConfig();
        $em->persist($config);
        $em->flush();

        $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->addOverviews($config);

        return $config;
    }

    /**
     * @param bool $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function flushConfig($id = false)
    {
        $em = $this->getEntityManager();

        // Backward compatibilty to flush overviews without a config set
        if (!$id) {
            $overviewRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
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
     * @param bool $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setUpdated($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setLastUpdate(new \DateTime());
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string $token
     * @param bool   $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveToken($token, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setToken($token);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string $propertyId
     * @param bool   $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function savePropertyId($propertyId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setPropertyId($propertyId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string $accountId
     * @param bool   $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveAccountId($accountId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setAccountId($accountId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string $profileId
     * @param bool   $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveProfileId($profileId, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setProfileId($profileId);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param string $name
     * @param bool   $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveConfigName($name, $id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setName($name);
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param bool $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetProfileId($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setProfileId('');
        $em->persist($config);
        $em->flush();
    }

    /**
     * @param bool $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetPropertyId($id = false)
    {
        $em = $this->getEntityManager();
        $config = $id ? $this->find($id) : $this->findFirst();
        $config->setAccountId('');
        $config->setProfileId('');
        $config->setPropertyId('');
        $em->persist($config);
        $em->flush();
    }
}
