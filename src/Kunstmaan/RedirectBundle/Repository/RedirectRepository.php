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

        $qb = $conn->createQueryBuilder();
        $qb->select('id')
            ->from('kuma_redirects')
            ->where(
                $qb->expr()->and(
                    $qb->expr()->like('origin_pattern', ':path'),
                    "REPLACE(origin_prefix, '%', '') = LEFT(:path, CHAR_LENGTH(REPLACE(origin_prefix, '%', '')))",
                )
            )
            ->andWhere(
                $qb->expr()->or(
                    $qb->expr()->eq('domain', ':domain'),
                    $qb->expr()->isNull('domain'),
                    $qb->expr()->eq('domain', "''")
                )
            )
            ->orderBy("CASE WHEN domain = :domain THEN 1 ELSE 0 END", "DESC")
            ->setMaxResults(1)
            ->setParameter('path', $path)
            ->setParameter('domain', $domain);

        $statement = $conn->prepare($qb->getSQL());
        foreach ($qb->getParameters() as $key => $value) {
            $statement->bindValue($key, $value);
        }

        $redirectId = method_exists($statement, 'executeQuery')
            ? $statement->executeQuery()->fetchOne()
            : $statement->execute()->fetchColumn();

        if (!$redirectId) {
            return null;
        }

        return $this->find($redirectId);
    }
}
