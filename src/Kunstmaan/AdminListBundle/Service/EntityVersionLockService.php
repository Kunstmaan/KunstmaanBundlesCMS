<?php

namespace Kunstmaan\AdminListBundle\Service;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\User;
use Kunstmaan\AdminBundle\Entity\UserInterface;
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
    public function isEntityLocked(/*\Kunstmaan\AdminBundle\Entity\UserInterface*/ $user, LockableEntityInterface $entity)
    {
        // NEXT_MAJOR: remove type check and enable parameter typehint
        if (!$user instanceof User && !$user instanceof UserInterface) {
            throw new \InvalidArgumentException(sprintf('The "$user" argument must be of type "%s" or implement the "%s" interface', User::class, UserInterface::class));
        }

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
     *
     * @param User           $user
     * @param LockableEntity $entity
     */
    protected function createEntityVersionLock(/*\Kunstmaan\AdminBundle\Entity\UserInterface*/ $user, LockableEntity $entity)
    {
        // NEXT_MAJOR: remove type check and enable parameter typehint
        if (!$user instanceof User && !$user instanceof UserInterface) {
            throw new \InvalidArgumentException(sprintf('The "$user" argument must be of type "%s" or implement the "%s" interface', User::class, UserInterface::class));
        }

        /** @var EntityVersionLock $lock */
        $lock = $this->objectManager->getRepository(EntityVersionLock::class)->findOneBy([
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
    public function getUsersWithEntityVersionLock(LockableEntityInterface $entity, /*\Kunstmaan\AdminBundle\Entity\UserInterface*/ $userToExclude = null)
    {
        // NEXT_MAJOR: remove type check and enable parameter typehint
        if (!$userToExclude instanceof User && !$userToExclude instanceof UserInterface) {
            throw new \InvalidArgumentException(sprintf('The "$userToExclude" argument must be of type "%s" or implement the "%s" interface', User::class, UserInterface::class));
        }

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
        $locks = $this->objectManager->getRepository(EntityVersionLock::class)->getExpiredLocks($entity, $this->threshold);
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
    protected function getEntityVersionLocksByLockableEntity(LockableEntity $entity, /*\Kunstmaan\AdminBundle\Entity\UserInterface*/ $userToExclude = null)
    {
        // NEXT_MAJOR: remove type check and enable parameter typehint
        if (!$userToExclude instanceof User && !$userToExclude instanceof UserInterface) {
            throw new \InvalidArgumentException(sprintf('The "$userToExclude" argument must be of type "%s" or implement the "%s" interface', User::class, UserInterface::class));
        }

        /** @var EntityVersionLockRepository $objectRepository */
        $objectRepository = $this->objectManager->getRepository(EntityVersionLock::class);

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
        $lockable = $this->objectManager->getRepository(LockableEntity::class)->getOrCreate($entity->getId(), \get_class($entity));

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
