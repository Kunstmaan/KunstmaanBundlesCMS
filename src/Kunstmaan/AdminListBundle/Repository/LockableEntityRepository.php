<?php

namespace Kunstmaan\AdminListBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminListBundle\Entity\LockableEntity;

/**
 * LockableEntityRepository
 */
class LockableEntityRepository extends EntityRepository
{
    /**
     * @param int    $id
     * @param string $class
     *
     * @return LockableEntity
     */
    public function getOrCreate($id, $class)
    {
        /** @var LockableEntity $lockable */
        $lockable = $this->findOneBy(['entityId' => $id, 'entityClass' => $class]);

        if ($lockable === null) {
            $lockable = new LockableEntity();
            $lockable->setEntityClass($class);
            $lockable->setEntityId($id);
        }

        return $lockable;
    }
}
