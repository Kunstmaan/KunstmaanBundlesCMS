<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\EntityManager;

/**
 * DeepCloneableInterface
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
