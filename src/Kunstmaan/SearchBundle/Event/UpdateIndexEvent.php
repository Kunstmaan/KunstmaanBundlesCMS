<?php

namespace Kunstmaan\SearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event used when the index needs to be updated by the changed object
 */
class UpdateIndexEvent extends Event {

    private $object;

    /**
     * @param $object   The object to update in the index
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    /**
     * @return The object
     */
    public function getObject()
    {
        return $this->object;
    }

}