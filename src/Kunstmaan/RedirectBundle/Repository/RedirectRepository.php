<?php

namespace Kunstmaan\RedirectBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\RedirectBundle\Entity\Redirect;

class RedirectRepository extends EntityRepository
{
    public function findByRequestPathAndDomain(string $path, string $domain): ?Redirect
    {
        $conn = $this->_em->getConnection();

        // Query 1: exact domain match
        $qb1 = $this->createRedirectsQueryBuilder($conn)
            ->where('domain = :domain')
            ->andWhere(':path LIKE origin_pattern')
            ->setParameter('path', $path)
            ->setParameter('domain', $domain);

        // Query 2: empty or NULL domain match
        $qb2 = $this->createRedirectsQueryBuilder($conn)
            ->where('COALESCE(domain, \'\') = \'\'')
            ->andWhere(':path LIKE origin_pattern')
            ->setParameter('path', $path);

        $sql = sprintf(
            '(%s) UNION ALL (%s) LIMIT 1',
            $qb1->getSQL(),
            $qb2->getSQL()
        );

        $redirectId = $conn->prepare($sql)->executeQuery($qb1->getParameters() + $qb2->getParameters())->fetchOne();
        if (!$redirectId) {
            return null;
        }

        return $this->find($redirectId);
    }

    private function createRedirectsQueryBuilder(Connection $connection): QueryBuilder
    {
        return $connection->createQueryBuilder()->select('id')->from('kuma_redirects');
    }
}
