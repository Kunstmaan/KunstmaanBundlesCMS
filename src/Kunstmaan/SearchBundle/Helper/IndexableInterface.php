<?php

namespace Kunstmaan\SearchBundle\Helper;

/**
 * Interface for your class to allow you to decide if the object should be indexed or not
 */
interface IndexableInterface
{
    /**
     * @return boolean
     */
    public function isIndexable();
}
