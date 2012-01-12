<?php

namespace Kunstmaan\AdminBundle\Repository;

use Kunstmaan\AdminBundle\Entity\Permission;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class PermissionRepository extends EntityRepository
{
    public function getPermission($type, $refId, array $groupIds, $permission, EntityManager $em)
    {
        $query = $em->createQuery(
            '   SELECT
                    p
                FROM
                    KunstmaanAdminBundle:permission p
                WHERE
                    p.refId = :refid
                AND
                    p.refEntityname = :type
                AND
                    p.refGroup IN(:groupIds)
                AND
                    p.permissions LIKE :permission'
        )->setParameters(array(
            'refid'         => $refId,
            'type'          => $type,
            'groupIds'      => implode(',', $groupIds),
            'permission'    => '%|'.$permission.':1|%'
        ));

        return $query->getSingleResult();
    }
}