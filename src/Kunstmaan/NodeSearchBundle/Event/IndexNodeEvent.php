<?php

namespace Kunstmaan\NodeSearchBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Symfony\Component\EventDispatcher\Event;

class IndexNodeEvent extends Event
{
    const EVENT_INDEX_NODE = 'kunstmaan_node_search.onIndexNode';

    /**
     * @var array
     */
    public $doc;

    /**
     * @var HasNodeInterface
     */
    private $page;

    public function __construct(HasNodeInterface $page, array $doc)
    {
        $this->doc = $doc;
        $this->page = $page;
    }

    /**
     * @return HasNodeInterface
     */
    public function getPage()
    {
        return $this->page;
    }
}
