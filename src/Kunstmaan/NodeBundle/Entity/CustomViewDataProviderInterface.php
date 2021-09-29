<?php

namespace Kunstmaan\NodeBundle\Entity;

/**
 * Implement this interface on pages to provide a service to add extra data for the view render.
 */
interface CustomViewDataProviderInterface
{
    /**
     * Return the service id of the provider service.
     */
    public function getViewDataProviderServiceId(): string;
}
