<?php

namespace Kunstmaan\NodeBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class SlugSecurityEvent
 */
final class SlugSecurityEvent extends Event
{
    private $node;

    private $nodeTranslation;

    private $entity;

    private $request;

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * @param mixed $nodeTranslation
     */
    public function setNodeTranslation($nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param mixed $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param mixed $request
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}
