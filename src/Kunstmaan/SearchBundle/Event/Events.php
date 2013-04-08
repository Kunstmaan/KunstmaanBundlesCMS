<?php

namespace Kunstmaan\SearchBundle\Event;

/**
 * SearchBundle Events
 */
class Events {

    /**
     * The onUpdateIndex event occurs for a given object when its indexable content has changed
     * and the index needs to be updated in order to correspond the new data
     */
    const UPDATE_INDEX = 'kunstmaan_search.onUpdateIndex';



}