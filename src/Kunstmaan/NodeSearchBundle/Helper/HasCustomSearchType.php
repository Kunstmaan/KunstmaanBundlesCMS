<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

/**
 * Implement this interface to override the default 'type' (class name) to be indexed for this class.
 */
interface HasCustomSearchType {

    /**
     * Returns the type as a string to be indexed for this page.
     *
     * @return string
     */
    public function getSearchType();

}