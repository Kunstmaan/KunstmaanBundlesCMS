<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface ChangeableLimitInterface
 */
interface ChangeableLimitInterface
{
    /**
     * Bind current request.
     *
     * @param Request $request
     */
    public function bindRequest(Request $request);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return array
     */
    public function getLimitOptions();
}
