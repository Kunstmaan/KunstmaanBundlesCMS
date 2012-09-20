<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Symfony\Component\HttpFoundation\Request;

interface AdminListFilterInterface
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param array                                     $data
     * @param string                                    $uniqueId
     */
    public function bindRequest(Request $request, &$data, $uniqueId);

    /**
     * @param        $queryBuilder
     * @param array  $expressions
     * @param array  $data
     * @param string $uniqueId
     */
    public function adaptQueryBuilder($queryBuilder, &$expressions, $data, $uniqueId);

    /**
     * @return string
     */
    public function getTemplate();
}
