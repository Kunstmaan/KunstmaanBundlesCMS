<?php

namespace  Kunstmaan\AdminBundle\Traits\DependencyInjection;

use Kunstmaan\AdminListBundle\Service\EntityVersionLockService;

/**
 * Trait EntityVersionLockTrait
 */
trait EntityVersionLockTrait
{
    /**
     * @var EntityVersionLockService
     */
    protected $entityVersionLockService;
    /**
     * @var int
     */
    protected $entityVersionLockInterval = 15;
    /**
     * @var bool
     */
    protected $entityVersionLockCheck = false;

    /**
     * @param bool $entityVersionLockCheck
     */
    public function setEntityVersionLockCheck($entityVersionLockCheck)
    {
        $this->entityVersionLockCheck = $entityVersionLockCheck;
    }

    /**
     * @param int $entityVersionLockInterval
     */
    public function setEntityVersionLockInterval($entityVersionLockInterval)
    {
        $this->entityVersionLockInterval = $entityVersionLockInterval;
    }

    /**
     * @param EntityVersionLockService $entityVersionLockService
     */
    public function setEntityVersionLockService(EntityVersionLockService $entityVersionLockService)
    {
        $this->entityVersionLockService = $entityVersionLockService;
    }

    /**
     * @return int
     */
    public function getEntityVersionLockInterval()
    {
        return $this->entityVersionLockInterval;
    }

    /**
     * @return bool
     */
    public function isEntityVersionLockCheck()
    {
        return $this->entityVersionLockCheck;
    }

    /**
     * @return EntityVersionLockService
     */
    public function getEntityVersionLockService()
    {
        if (null !== $this->container && null === $this->entityVersionLockService) {
            $this->eventDispatcher = $this->container->get('kunstmaan_entity.admin_entity.entity_version_lock_service');
        }

        return $this->entityVersionLockService;
    }

}
