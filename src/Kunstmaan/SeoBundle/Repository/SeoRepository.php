<?php

namespace Kunstmaan\SeoBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
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
     * @param EntityInterface $entity
     *
     * @return Seo
     */
    public function findFor(EntityInterface $entity)
    {
        /** @var Seo $seo */
        $seo = $this->findOneBy([
            'refId' => $entity->getId(),
            'refEntityName' => ClassLookup::getClass($entity)
        ]);
        return $seo;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return Seo
     */
    public function findOrCreateFor(EntityInterface $entity)
    {
        $seo = $this->findFor($entity);

        if (is_null($seo)) {
            $seo = new Seo();
            $seo->setRef($entity);
        }

        return $seo;
    }
}
