<?php

namespace Kunstmaan\NodeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

/**
 * PageEvent
 */
class PageEvent extends Event
{
    /**
     * @var HasNodeInterface
     */
    protected $page;

    /**
     * @var Node
     */
    protected $node;

    /**
     * @var NodeVersion
     */
    protected $nodeVersion;

    /**
     * @var NodeTranslation
     */
    protected $nodeTranslation;

    /**
     * @param Node             $node            The node
     * @param NodeTranslation  $nodeTranslation The nodetranslation
     * @param NodeVersion      $nodeVersion     The node version
     * @param HasNodeInterface $page            The object
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion, HasNodeInterface $page)
    {
        $this->node            = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->nodeVersion     = $nodeVersion;
        $this->page            = $page;
    }

    /**
     * @param NodeVersion $nodeVersion
     *
     * @return PageEvent
     */
    public function setNodeVersion($nodeVersion)
    {
        $this->nodeVersion = $nodeVersion;
        return $this;
    }

    /**
     * @return NodeVersion
     */
    public function getNodeVersion()
    {
        return $this->nodeVersion;
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
