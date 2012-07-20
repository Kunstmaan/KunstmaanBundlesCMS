<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Event;

use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;

use Symfony\Component\EventDispatcher\Event;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Kunstmaan\AdminNodeBundle\Entity\Node;

/**
 * PageEvent
 */
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
     * @return \Kunstmaan\AdminNodeBundle\Entity\Node
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
     * @return \Kunstmaan\AdminNodeBundle\Entity\NodeTranslation
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
