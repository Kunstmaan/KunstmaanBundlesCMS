<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\EntityManager;

interface DeepCloneableIFace
{
	public function deepClone(EntityManager $em);
}
