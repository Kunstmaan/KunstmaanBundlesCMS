<?php

namespace Kunstmaan\DashboardBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;

/**
 * Class AnalyticsSegmentRepository
 */
class AnalyticsSegmentRepository extends EntityRepository
{
    /**
     * @param $id
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function deleteSegment($id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s')
            ->from('KunstmaanDashboardBundle:AnalyticsSegment', 's')
            ->where('s.id = :id')
            ->setParameter('id', $id);


        $results = $qb->getQuery()->getResult();
        if ($results) {
            $em->remove($results[0]);
            $em->flush();
        }
    }

    /**
     * @param AnalyticsSegment $segment
     * @param int|null         $configId
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function initSegment(AnalyticsSegment $segment, $configId = null)
    {
        if (!\count($segment->getOverviews()->toArray())) {
            if ($configId) {
                $config = $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->find($configId);
            } else {
                $config = $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst();
            }
            $this->getEntityManager()->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->addOverviews($config, $segment);
        }
    }

}
