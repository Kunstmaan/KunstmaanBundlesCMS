<?php

namespace Kunstmaan\RedirectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\RedirectBundle\Entity\Redirect;

class RedirectRepository extends EntityRepository
{
    public function findByRequestPathAndDomain(string $path, string $domain): ?Redirect
    {
        $conn = $this->_em->getConnection();
        $qb = $conn->createQueryBuilder();

        $qb
            ->select('redirect.id')
            ->from('kuma_redirects', 'redirect')
            // This allows to easily match the current request path with wildcard origin values in the redirect table.
            ->where(':path LIKE REPLACE(redirect.origin, \'*\', \'%\')')
            ->andWhere($qb->expr()->or('redirect.domain IS NULL', 'redirect.domain = \'\'', 'redirect.domain = :domain'))
            ->setParameter('path', $path)
            ->setParameter('domain', $domain)
        ;

        $redirectId = method_exists($qb, 'executeQuery') ? $qb->executeQuery()->fetchOne() : $qb->execute()->fetchColumn();
        if (null === $redirectId) {
            return null;
        }

        return $this->find($redirectId);
    }
}
