<?php

namespace Kunstmaan\AdminBundle\Helper\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * The MenuBuilder will build the top menu and the side menu of the admin interface
 */
class MenuBuilder
{
    /**
     * @var MenuAdaptorInterface[]
     */
    private $adaptors = [];

    /**
     * @var MenuAdaptorInterface[]
     */
    private $sorted = [];

    /**
     * @var TopMenuItem[]
     */
    private $topMenuItems;

    /**
     * @var MenuItem|null
     */
    private $currentCache;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Add menu adaptor
     */
    public function addAdaptMenu(MenuAdaptorInterface $adaptor, $priority = 0)
    {
        $this->adaptors[$priority][] = $adaptor;
        unset($this->sorted);
    }

    /**
     * Get current menu item
     *
     * @return MenuItem|null
     */
    public function getCurrent()
    {
        if ($this->currentCache !== null) {
            return $this->currentCache;
        }
        /* @var $active MenuItem */
        $active = null;
        do {
            /* @var MenuItem[] $children */
            $children = $this->getChildren($active);
            $foundActiveChild = false;
            foreach ($children as $child) {
                if ($child->getActive()) {
                    $foundActiveChild = true;
                    $active = $child;

                    break;
                }
            }
        } while ($foundActiveChild);
        $this->currentCache = $active;

        return $active;
    }

    /**
     * Get breadcrumb path for current menu item
     *
     * @return MenuItem[]
     */
    public function getBreadCrumb()
    {
        $result = [];
        $current = $this->getCurrent();
        while (!\is_null($current)) {
            array_unshift($result, $current);
            $current = $current->getParent();
        }

        return $result;
    }

    /**
     * Get top parent menu of current menu item
     *
     * @return TopMenuItem|null
     */
    public function getLowestTopChild()
    {
        $current = $this->getCurrent();
        while (!\is_null($current)) {
            if ($current instanceof TopMenuItem) {
                return $current;
            }
            $current = $current->getParent();
        }

        return null;
    }

    /**
     * Get all top menu items
     *
     * @return MenuItem[]
     */
    public function getTopChildren()
    {
        if (\is_null($this->topMenuItems)) {
            /* @var $request Request */
            $request = $this->requestStack->getCurrentRequest();
            $this->topMenuItems = [];
            foreach ($this->getAdaptors() as $menuAdaptor) {
                $menuAdaptor->adaptChildren($this, $this->topMenuItems, null, $request);
            }
        }

        return $this->topMenuItems;
    }

    /**
     * Get immediate children of the specified menu item
     *
     * @return MenuItem[]
     */
    public function getChildren(?MenuItem $parent = null)
    {
        if ($parent === null) {
            return $this->getTopChildren();
        }
        /* @var $request Request */
        $request = $this->requestStack->getCurrentRequest();
        $result = [];
        foreach ($this->getAdaptors() as $menuAdaptor) {
            $menuAdaptor->adaptChildren($this, $result, $parent, $request);
        }

        usort($result, function ($a, $b) {
            return $a->getWeight() <=> $b->getWeight();
        });

        return $result;
    }

    private function getAdaptors()
    {
        if (!isset($this->sorted)) {
            $this->sortAdaptors();
        }

        return $this->sorted;
    }

    /**
     * Sorts the internal list of adaptors by priority.
     */
    private function sortAdaptors()
    {
        $this->sorted = [];

        if (isset($this->adaptors)) {
            krsort($this->adaptors);
            $this->sorted = array_merge(...$this->adaptors);
        }
    }
}
