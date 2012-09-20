<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\Request;

/**
 * AdminListFilterInterface
 */
interface AdminListFilterInterface
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, &$data, $uniqueId);

    /**
     * @param QueryBuilder $queryBuilder The query builder
     * @param array        &$expressions The expressions
     * @param array        $data         The data
     * @param string       $uniqueId     The unique identifier
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, &$expressions, $data, $uniqueId);

    /**
     * @return string
     */
    public function getTemplate();
}
