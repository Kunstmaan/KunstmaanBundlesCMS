<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

final class SlugSecurityEvent extends Event
{
    /** @var Node|null */
    private $node;
    /** @var NodeTranslation|null */
    private $nodeTranslation;
    /** @var HasNodeInterface|null */
    private $entity;
    /** @var Request|null */
    private $request;

    public function getNode(): ?Node
    {
        return $this->node;
    }

    /**
     * @param Node $node
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    public function getNodeTranslation(): ?NodeTranslation
    {
        return $this->nodeTranslation;
    }

    /**
     * @param NodeTranslation $nodeTranslation
     */
    public function setNodeTranslation($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    public function getEntity(): ?HasNodeInterface
    {
        return $this->entity;
    }

    /**
     * @param HasNodeInterface $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}
