<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\HttpFoundation\Response;

final class PreNodeDuplicateEvent extends BcEvent
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var Response
     */
    private $response;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function setNode(Node $node): self
    {
        $this->node = $node;

        return $this;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function setResponse(Response $response): self
    {
        $this->response = $response;

        return $this;
    }
}
