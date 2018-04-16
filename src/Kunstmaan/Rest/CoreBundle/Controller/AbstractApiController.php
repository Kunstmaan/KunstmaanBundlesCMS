<?php

/*
 * This file is part of the KunstmaanBundlesCMS package.
 *
 * (c) Kunstmaan <https://github.com/Kunstmaan/KunstmaanBundlesCMS/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\Rest\CoreBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Kunstmaan\Rest\CoreBundle\Model\PaginatedCollection;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Class AbstractApiController
 */
abstract class AbstractApiController extends FOSRestController
{
    /**
     * Create an ORM Paginated collection containing items, count and total
     *
     * @param QueryBuilder  $qb
     * @param               $page
     * @param               $limit
     * @param \Closure|null $decorator
     *
     * @return PaginatedCollection
     */
    protected function createORMPaginatedCollection(QueryBuilder $qb, $page, $limit, \Closure $decorator = null)
    {
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($limit);
        $pagerfanta->setCurrentPage($page);

        $items = [];
        foreach ($pagerfanta->getCurrentPageResults() as $result) {
            $items[] = $decorator !== null ? $decorator($result) : $result;
        }

        return new PaginatedRepresentation(
            new CollectionRepresentation($items),
            'get_nodes',
            [],
            $page,
            $limit,
            $pagerfanta->getNbPages(),
            null,
            null,
            false,
            $pagerfanta->getNbResults()
        );
    }
}
