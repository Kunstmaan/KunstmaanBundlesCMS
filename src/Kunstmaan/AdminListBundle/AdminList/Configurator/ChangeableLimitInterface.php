<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Symfony\Component\HttpFoundation\Request;

interface ChangeableLimitInterface
{
    /**
     * Bind current request.
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
