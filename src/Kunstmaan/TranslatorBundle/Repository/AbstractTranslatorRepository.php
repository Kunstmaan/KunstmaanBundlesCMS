<?php
namespace Kunstmaan\TranslatorBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Translator Repository class
 */
class AbstractTranslatorRepository extends EntityRepository
{
    public function flush($entity = null)
    {
        if ($entity !== null) {
            $this->getEntityManager()->persist($entity);
        }

        $this->getEntityManager()->flush();
    }
}
