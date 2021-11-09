<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\Common\Collections\Collection;

class NodeIterator implements \RecursiveIterator
{
    private $_data;

    /**
     * @param Collection<Node> $data
     */
    public function __construct(Collection $data)
    {
        $this->_data = $data;
    }

    public function hasChildren(): bool
    {
        return !$this->_data->current()->getChildren()->isEmpty();
    }

    public function getChildren(): \RecursiveIterator
    {
        return new NodeIterator($this->_data->current()->getChildren());
    }

    /**
     * @return Node|false
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->_data->current();
    }

    public function next(): void
    {
        $this->_data->next();
    }

    public function key(): int
    {
        return $this->_data->key();
    }

    public function valid(): bool
    {
        return $this->_data->current() instanceof Node;
    }

    public function rewind(): void
    {
        $this->_data->first();
    }
}
