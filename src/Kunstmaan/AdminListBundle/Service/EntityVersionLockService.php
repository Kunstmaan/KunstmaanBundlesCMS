<?php

namespace Kunstmaan\AdminListBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\User;
use Kunstmaan\AdminListBundle\Entity\LockableEntity;
use Kunstmaan\AdminListBundle\Entity\EntityVersionLock;
use Kunstmaan\AdminListBundle\Entity\LockableEntityInterface;
use Kunstmaan\AdminListBundle\Repository\EntityVersionLockRepository;

class EntityVersionLockService
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var int
     */
    private $threshold;

    /**
     * @var bool
     */
    private $lockEnabled;

    public function __construct(ObjectManager $em, $threshold, $lockEnabled)
    {
        $this->setObjectManager($em);
        $this->setThreshold($threshold);
        $this->setLockEnabled($lockEnabled);
    }

    /**
     * @param LockableEntityInterface $entity
     *
     * @return bool
     */
    public function isEntityBelowThreshold(LockableEntityInterface $entity)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->getLockableEntity($entity, false);

        if ($this->lockEnabled && $lockable->getId() !== null) {
            $now = new \DateTime();
            $thresholdDate = clone $lockable->getUpdated();
            $thresholdDate->add(new \DateInterval('PT'.$this->threshold.'S'));

            return $thresholdDate > $now;
        }

        return false;
    }

    /**
     * @param User                    $user
     * @param LockableEntityInterface $entity
     *
     * @return bool
     */
    public function isEntityLocked(User $user, LockableEntityInterface $entity)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->getLockableEntity($entity);

        if ($this->lockEnabled) {
            $this->removeExpiredLocks($lockable);
            $locks = $this->getEntityVersionLocksByLockableEntity($lockable, $user);

            if ($locks === null || !count($locks)) {
                $this->createEntityVersionLock($user, $lockable);

                $lockable->setUpdated(new \DateTime());
                $this->objectManager->flush();

                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * When editing the entity, create a new entity translation lock.
     *
     * @param User           $user
     * @param LockableEntity $entity
     */
    protected function createEntityVersionLock(User $user, LockableEntity $entity)
    {
        /** @var EntityVersionLock $lock */
        $lock = $this->objectManager->getRepository('KunstmaanAdminListBundle:EntityVersionLock')->findOneBy([
            'owner' => $user->getUsername(),
            'lockableEntity' => $entity,
        ]);
        if (!$lock) {
            $lock = new EntityVersionLock();
        }
        $lock->setOwner($user->getUsername());
        $lock->setLockableEntity($entity);
        $lock->setCreatedAt(new \DateTime());
        $this->objectManager->persist($lock);
        $this->objectManager->flush();
    }

    /**
     * @param LockableEntityInterface $entity
     * @param User                    $userToExclude
     *
     * @return array
     */
    public function getUsersWithEntityVersionLock(LockableEntityInterface $entity, User $userToExclude = null)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->getLockableEntity($entity);

        return  array_reduce(
            $this->getEntityVersionLocksByLockableEntity($lockable, $userToExclude),
            function ($return, EntityVersionLock $item) {
                $return[] = $item->getOwner();

                return $return;
            },
            []
        );
    }

    /**
     * @param LockableEntity $entity
     */
    protected function removeExpiredLocks(LockableEntity $entity)
    {
        $locks = $this->objectManager->getRepository('KunstmaanAdminListBundle:EntityVersionLock')->getExpiredLocks($entity, $this->threshold);
        foreach ($locks as $lock) {
            $this->objectManager->remove($lock);
        }
    }

    /**
     * When editing an entity, check if there is a lock for this entity.
     *
     * @param LockableEntity $entity
     * @param User           $userToExclude
     *
     * @return EntityVersionLock[]
     */
    protected function getEntityVersionLocksByLockableEntity(LockableEntity $entity, User $userToExclude = null)
    {
        /** @var EntityVersionLockRepository $objectRepository */
        $objectRepository = $this->objectManager->getRepository('KunstmaanAdminListBundle:EntityVersionLock');

        return $objectRepository->getLocksForLockableEntity($entity, $this->threshold, $userToExclude);
    }

    /**
     * Get or create a LockableEntity for an entity with LockableEntityInterface
     *
     * @param LockableEntityInterface $entity
     *
     * @return LockableEntity
     */
    protected function getLockableEntity(LockableEntityInterface $entity, $create = true)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->objectManager->getRepository('KunstmaanAdminListBundle:LockableEntity')->getOrCreate($entity->getId(), get_class($entity));

        if ($create === true && $lockable->getId() === null) {
            $this->objectManager->persist($lockable);
            $this->objectManager->flush();
        }

        return $lockable;
    }

    /**
     * @param ObjectManager $objectManager
     */
    public function setObjectManager($objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param int $threshold
     */
    public function setThreshold($threshold)
    {
        $this->threshold = $threshold;
    }

    /**
     * @param bool lockEnabled
     */
    public function setLockEnabled($lockEnabled)
    {
        $this->lockEnabled = $lockEnabled;
    }
}
