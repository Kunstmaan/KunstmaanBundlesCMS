<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Event;

use Symfony\Component\EventDispatcher\Event;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Kunstmaan\AdminNodeBundle\Entity\Node;

class PageEvent extends Event
{
    /**
     * @var
     */
    protected $page;
    /**
     * @var \Kunstmaan\AdminNodeBundle\Entity\Node
     */
    protected $node;
    /**
     * @var \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation
     */
    protected $nodeTranslation;


    /**
     * @param \Kunstmaan\AdminNodeBundle\Entity\Node $node
     * @param \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation $nodeTranslation
     * @param $page
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, $page)
    {
        $this->node            = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->page            = $page;
    }

    /**
     * @param $node
     *
     * @return PageEvent
     */
    public function setNode($node)
    {
        $this->node = $node;
        return $this;
    }

    /**
     * @return \Kunstmaan\AdminNodeBundle\Entity\Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param $nodeTranslation
     *
     * @return PageEvent
     */
    public function setNodeTranslation($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;
        return $this;
    }

    /**
     * @return \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * @param $page
     *
     * @return PageEvent
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPage()
    {
        return $this->page;
    }

}
