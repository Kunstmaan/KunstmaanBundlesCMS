<?php

namespace Kunstmaan\SearchBundle\Helper;

/**
 * Interface for your class to control if the object should be indexed or not
 */
interface IndexControllerInterface
{
    /**
     * @return boolean
     */
    public function shouldBeIndexed();

}
