<?php

namespace Kunstmaan\AdminBundle\Repository;

use Doctrine\ORM\EntityManager;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    public function findAllWithPermissions($refId, $refEntityName)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(array('p', 'g'))
            ->from('\Kunstmaan\AdminBundle\Entity\Permission', 'p')
            ->innerJoin('p.refGroup', 'g');

        $query = $qb->getQuery();
        $results = $query->getResult();
        var_dump($results);die;


        $query = $this->getEntityManager()->createQuery(
            '   SELECT
                    p, g
                FROM
                    KunstmaanAdminBundle:permission p
                    INNER JOIN
                      user_group g
                      ON
                        g.id = p.refGroup
                WHERE
                    p.refId = :refid
                AND
                    p.refEntityname = :type'
        )->setParameters(array(
            'refid'         => $refId,
            'type'          => $refEntityName,
        ));

        return $query->getResult();
    }
}