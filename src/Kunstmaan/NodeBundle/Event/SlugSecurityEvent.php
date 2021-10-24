<?php

namespace Kunstmaan\NodeBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\HttpFoundation\Request;

final class SlugSecurityEvent extends BcEvent
{
    /** @var Node|null */
    private $node;
    /** @var NodeTranslation|null */
    private $nodeTranslation;
    /** @var HasNodeInterface|null */
    private $entity;
    /** @var Request|null */
    private $request;

    /**
     * @return Node|null
     */
    public function getNode()
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

    /**
     * @return NodeTranslation|null
     */
    public function getNodeTranslation()
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

    /**
     * @return HasNodeInterface|null
     */
    public function getEntity()
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

    /**
     * @return Request|null
     */
    public function getRequest()
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
