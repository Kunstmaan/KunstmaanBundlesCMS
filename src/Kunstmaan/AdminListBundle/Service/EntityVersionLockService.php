<?php

namespace Kunstmaan\AdminListBundle\Service;

use Doctrine\Persistence\ObjectManager;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminListBundle\Entity\EntityVersionLock;
use Kunstmaan\AdminListBundle\Entity\LockableEntity;
use Kunstmaan\AdminListBundle\Entity\LockableEntityInterface;
use Kunstmaan\AdminListBundle\Repository\EntityVersionLockRepository;

class EntityVersionLockService
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var int */
    private $threshold;

    /** @var bool */
    private $lockEnabled;

    public function __construct(ObjectManager $em, int $threshold, bool $lockEnabled)
    {
        $this->objectManager = $em;
        $this->threshold = $threshold;
        $this->lockEnabled = $lockEnabled;
    }

    /**
     * @return bool
     */
    public function isEntityBelowThreshold(LockableEntityInterface $entity)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->getLockableEntity($entity, false);

        if ($this->lockEnabled && $lockable->getId() !== null) {
            $now = new \DateTime();
            $thresholdDate = clone $lockable->getUpdated();
            $thresholdDate->add(new \DateInterval('PT' . $this->threshold . 'S'));

            return $thresholdDate > $now;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isEntityLocked(UserInterface $user, LockableEntityInterface $entity)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->getLockableEntity($entity);

        if ($this->lockEnabled) {
            $this->removeExpiredLocks($lockable);
            $locks = $this->getEntityVersionLocksByLockableEntity($lockable, $user);

            if ($locks === null || !\count($locks)) {
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
     */
    protected function createEntityVersionLock(UserInterface $user, LockableEntity $entity)
    {
        /** @var EntityVersionLock $lock */
        $lock = $this->objectManager->getRepository(EntityVersionLock::class)->findOneBy([
            'owner' => method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername(),
            'lockableEntity' => $entity,
        ]);
        if (!$lock) {
            $lock = new EntityVersionLock();
        }
        $lock->setOwner(method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : $user->getUsername());
        $lock->setLockableEntity($entity);
        $lock->setCreatedAt(new \DateTime());
        $this->objectManager->persist($lock);
        $this->objectManager->flush();
    }

    /**
     * @return array
     */
    public function getUsersWithEntityVersionLock(LockableEntityInterface $entity, UserInterface $userToExclude = null)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->getLockableEntity($entity);

        return array_reduce(
            $this->getEntityVersionLocksByLockableEntity($lockable, $userToExclude),
            function ($return, EntityVersionLock $item) {
                $return[] = $item->getOwner();

                return $return;
            },
            []
        );
    }

    protected function removeExpiredLocks(LockableEntity $entity)
    {
        $locks = $this->objectManager->getRepository(EntityVersionLock::class)->getExpiredLocks($entity, $this->threshold);
        foreach ($locks as $lock) {
            $this->objectManager->remove($lock);
        }
    }

    /**
     * When editing an entity, check if there is a lock for this entity.
     *
     * @return EntityVersionLock[]
     */
    protected function getEntityVersionLocksByLockableEntity(LockableEntity $entity, UserInterface $userToExclude = null)
    {
        /** @var EntityVersionLockRepository $objectRepository */
        $objectRepository = $this->objectManager->getRepository(EntityVersionLock::class);

        return $objectRepository->getLocksForLockableEntity($entity, $this->threshold, $userToExclude);
    }

    /**
     * Get or create a LockableEntity for an entity with LockableEntityInterface
     *
     * @return LockableEntity
     */
    protected function getLockableEntity(LockableEntityInterface $entity, $create = true)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->objectManager->getRepository(LockableEntity::class)->getOrCreate($entity->getId(), \get_class($entity));

        if ($create === true && $lockable->getId() === null) {
            $this->objectManager->persist($lockable);
            $this->objectManager->flush();
        }

        return $lockable;
    }
}
