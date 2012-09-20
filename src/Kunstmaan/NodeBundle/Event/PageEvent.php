<?php

namespace Kunstmaan\AdminNodeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

/**
 * PageEvent
 */
class PageEvent extends Event
{
    /* @var HasNodeInterface $page */
    protected $page;

    /* @var Node $node */
    protected $node;

    /* @var NodeTranslation $nodeTranslation */
    protected $nodeTranslation;

    /**
     * @param Node             $node            The node
     * @param NodeTranslation  $nodeTranslation The nodetranslation
     * @param HasNodeInterface $page            The object
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, HasNodeInterface $page)
    {
        $this->node            = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->page            = $page;
    }

    /**
     * @param Node $node
     *
     * @return PageEvent
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     *
     * @return PageEvent
     */
    public function setNodeTranslation($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    /**
     * @return NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return PageEvent
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return HasNodeInterface
     */
    public function getPage()
    {
        return $this->page;
    }
}
