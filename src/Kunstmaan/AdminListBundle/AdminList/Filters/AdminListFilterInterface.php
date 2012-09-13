<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Symfony\Component\HttpFoundation\Request;

interface AdminListFilterInterface
{
    public function bindRequest(Request $request, &$data, $uniqueId);
    public function adaptQueryBuilder($queryBuilder, &$expressions, $data, $uniqueId);
    public function getTemplate();
}
