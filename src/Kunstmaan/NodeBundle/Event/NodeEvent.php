<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

/**
 * NodeEvent
 */
class NodeEvent extends Event
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
     * @var Response
     */
    private $response = null;

    /**
     * @param Node $node The node
     * @param NodeTranslation $nodeTranslation The nodetranslation
     * @param NodeVersion $nodeVersion The node version
     * @param HasNodeInterface $page The object
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, NodeVersion $nodeVersion, HasNodeInterface $page)
    {
        $this->node = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->nodeVersion = $nodeVersion;
        $this->page = $page;
    }

    /**
     * @return NodeVersion
     */
    public function getNodeVersion()
    {
        return $this->nodeVersion;
    }

    /**
     * @param NodeVersion $nodeVersion
     *
     * @return NodeEvent
     */
    public function setNodeVersion($nodeVersion)
    {
        $this->nodeVersion = $nodeVersion;

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
     * @param Node $node
     *
     * @return NodeEvent
     */
    public function setNode($node)
    {
        $this->node = $node;

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
     * @param NodeTranslation $nodeTranslation
     *
     * @return NodeEvent
     */
    public function setNodeTranslation($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    /**
     * @return HasNodeInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param HasNodeInterface $page
     *
     * @return NodeEvent
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return NodeEvent
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }
}
