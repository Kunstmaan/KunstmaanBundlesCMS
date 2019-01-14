<?php

namespace Kunstmaan\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\SeoBundle\Entity\Seo;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Repository for Seo
 */
class SeoRepository extends EntityRepository
{
    /**
     * Find the seo information for the given entity
     *
     * @param AbstractEntity $entity
     *
     * @return Seo
     */
    public function findFor(AbstractEntity $entity)
    {
        return $this->findOneBy(array('refId' => $entity->getId(), 'refEntityName' => ClassLookup::getClass($entity)));
    }

    /**
     * @param AbstractEntity $entity
     *
     * @return Seo
     */
    public function findOrCreateFor(AbstractEntity $entity)
    {
        $seo = $this->findFor($entity);

        if (is_null($seo)) {
            $seo = new Seo();
            $seo->setRef($entity);
        }

        return $seo;
    }
}
