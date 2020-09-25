<?php

namespace Kunstmaan\NodeSearchBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

class IndexNodeEvent extends BcEvent
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
