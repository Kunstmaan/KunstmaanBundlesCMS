<?php

namespace Kunstmaan\NodeSearchBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class IndexNodeEvent
 *
 * @package Kunstmaan\NodeSearchBundle\Event
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
