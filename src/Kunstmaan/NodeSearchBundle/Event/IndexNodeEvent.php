<?php

namespace Kunstmaan\NodeSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Event triggered when a Node is being indexed. The document is being passed by reference to allow manipulation before being added to the index
 */
class IndexNodeEvent extends Event
{
    protected $page;

    public $doc;

    public function __construct($page, &$doc)
    {
        $this->page = $page;
        $this->doc = &$doc;
    }

    public function getPage()
    {
        return $this->page;
    }

}
