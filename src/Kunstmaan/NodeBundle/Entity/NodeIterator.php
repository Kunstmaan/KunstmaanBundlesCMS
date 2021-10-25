<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\Common\Collections\Collection;

class NodeIterator implements \RecursiveIterator
{
    private $_data;

    public function __construct(Collection $data)
    {
        $this->_data = $data;
    }

    /**
     * @return bool
     */
    #[\ReturnTypeWillChange]
    public function hasChildren()
    {
        return !$this->_data->current()->getChildren()->isEmpty();
    }

    /**
     * @return \RecursiveIterator
     */
    #[\ReturnTypeWillChange]
    public function getChildren()
    {
        return new NodeIterator($this->_data->current()->getChildren());
    }

    /**
     * @return Node
     */
    public function current()
    {
        return $this->_data->current();
    }

    /**
     * @return void
     */
    public function next()
    {
        $this->_data->next();
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->_data->key();
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return $this->_data->current() instanceof Node;
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->_data->first();
    }
}
