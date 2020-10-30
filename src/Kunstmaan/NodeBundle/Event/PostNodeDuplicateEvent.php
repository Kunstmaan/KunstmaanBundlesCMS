<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

final class PostNodeDuplicateEvent extends Event
{
    /**
     * @var Node
     */
    private $node;

    /**
     * @var Node
     */
    private $newNode;

    /**
     * @var HasNodeInterface
     */
    private $newPage;

    /**
     * @var Response
     */
    private $response;

    public function __construct(Node $node, Node $newNode, HasNodeInterface $newPage)
    {
        $this->node = $node;
        $this->newNode = $newNode;
        $this->newPage = $newPage;
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

    public function getNewNode(): Node
    {
        return $this->newNode;
    }

    public function setNewNode(Node $newNode): self
    {
        $this->newNode = $newNode;

        return $this;
    }

    public function getNewPage(): HasNodeInterface
    {
        return $this->newPage;
    }

    public function setNewPage(HasNodeInterface $newPage): self
    {
        $this->newPage = $newPage;

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
