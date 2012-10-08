<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\EntityManager;

/**
 * Entities implementing this interface will be able to be cloned and persisted
 */
interface DeepCloneableInterface
{
    /**
     * Deep clone the object and persist it.
     *
     * @param EntityManager $em
     */
    public function deepClone(EntityManager $em);
}
