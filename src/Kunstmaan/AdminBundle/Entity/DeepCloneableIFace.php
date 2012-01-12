<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminNodeBundle\Entity\HasNode;

interface DeepCloneableIFace
{
	public function deepClone(EntityManager $em);
}
