<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

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
     * @param array             $data     Data
     * @param string            $uniqueId The identifier
     */
    public function apply($data, $uniqueId);

    /**
     * @return string
     */
    public function getTemplate();
}
