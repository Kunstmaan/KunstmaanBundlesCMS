<?php

namespace Kunstmaan\AdminListBundle\AdminList\Filters;

use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\AdminListBundle\AdminList\Provider\ProviderInterface;

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
     * @param ProviderInterface $provider The provider
     * @param array             $data     Data
     * @param string            $uniqueId The identifier
     */
    public function apply(ProviderInterface $provider, $data, $uniqueId);

    /**
     * @return string
     */
    public function getTemplate();
}
