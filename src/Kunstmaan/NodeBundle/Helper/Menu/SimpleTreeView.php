<?php

namespace Kunstmaan\NodeBundle\Helper\Menu;

/**
 * Class SimpleTreeView
 */
class SimpleTreeView
{
    /**
     * @var array
     */
    private $items = array();

    /**
     * Add an item to the tree array
     *
     * @param int          $parentId
     * @param array|object $data
     */
    public function addItem($parentId, $data)
    {
        if (empty($parentId)) {
            $this->items[0][] = $data;
        } else {
            $this->items[$parentId][] = $data;
        }
    }

    /**
     * Get the top tree items.
     *
     * @return array
     */
    public function getRootItems()
    {
        return $this->getChildren(0);
    }

    /**
     * Get the child items for a tree item.
     *
     * @param int $parentId
     *
     * @return array
     */
    public function getChildren($parentId)
    {
        if (array_key_exists($parentId, $this->items)) {
            return $this->items[$parentId];
        } else {
            return array();
        }
    }
}
