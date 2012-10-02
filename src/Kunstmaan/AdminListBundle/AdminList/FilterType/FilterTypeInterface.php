<?php

namespace Kunstmaan\AdminListBundle\AdminList\FilterType;

use Symfony\Component\HttpFoundation\Request;

/**
 * FilterTypeInterface
 */
interface FilterTypeInterface
{
    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(Request $request, array &$data, $uniqueId);

    /**
     * @param array  $data     Data
     * @param string $uniqueId The identifier
     */
    public function apply(array $data, $uniqueId);

    /**
     * @return string
     */
    public function getTemplate();
}
