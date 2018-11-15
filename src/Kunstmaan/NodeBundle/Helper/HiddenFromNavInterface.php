<?php

namespace Kunstmaan\NodeBundle\Helper;

/**
 * Implement this interface to give you control over the navigation behavior of this page
 */
interface HiddenFromNavInterface
{
    /**
     * Returns true when the page is to be hidden from the navigation
     *
     * @return bool
     */
    public function isHiddenFromNav();
}
