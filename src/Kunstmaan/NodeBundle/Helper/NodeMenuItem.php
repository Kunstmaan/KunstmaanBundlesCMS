<?php

namespace Kunstmaan\NodeBundle\Helper;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

/**
 * NodeMenuItem
 */
class NodeMenuItem
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var NodeTranslation
     */
    private $nodeTranslation;

    /**
     * @var NodeMenuItem[]
     */
    private $children = null;

    /**
     * @var NodeMenuItem
     */
    private $parent;

    /**
     * @var NodeMenu
     */
    private $menu;

    /**
     * @param Node                    $node            The node
     * @param NodeTranslation         $nodeTranslation The nodetranslation
     * @param NodeMenuItem|null|false $parent          The parent nodemenuitem
     * @param NodeMenu                $menu            The menu
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, $parent = false, NodeMenu $menu)
    {
        $this->node = $node;
        $this->nodeTranslation = $nodeTranslation;
        // false = look up parent later if required (default); null = top menu item; NodeMenuItem = parent item already fetched
        $this->parent = $parent;
        $this->menu = $menu;
        $this->em = $menu->getEntityManager();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->node->getId();
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->menu->getLang();
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        $nodeTranslation = $this->getNodeTranslation();
        if ($nodeTranslation) {
            return $nodeTranslation->getTitle();
        }

        return "Untranslated";
    }

    /**
     * @return bool
     */
    public function getOnline()
    {
        $nodeTranslation = $this->getNodeTranslation();
        if ($nodeTranslation) {
            return $nodeTranslation->isOnline();
        }

        return false;
    }

    /**
     * @return string|NULL
     */
    public function getSlugPart()
    {
        $nodeTranslation = $this->getNodeTranslation();
        if ($nodeTranslation) {
            return $nodeTranslation->getFullSlug();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->getUrl();
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $nodeTranslation = $this->getNodeTranslation();
        if ($nodeTranslation) {
            return $nodeTranslation->getUrl();
        }

        return null;
    }

    /**
     * @return NodeMenuItem|NULL
     */
    public function getParent()
    {
        if ($this->parent === false) {
            // We need to calculate the parent
            $this->parent = $this->menu->getParent($this->node);
        }

        return $this->parent;
    }

    /**
     * @param NodeMenuItem|null|false $parent
     */
    public function setParent($parent = false)
    {
        $this->parent = $parent;
    }

    /**
     * @param string $class
     *
     * @return NodeMenuItem|NULL
     */
    public function getParentOfClass($class)
    {
        // Check for namespace alias
        if (strpos($class, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $class);
            $class = $this->em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
        }
        if ($this->getParent() === null) {
            return null;
        }
        if ($this->parent->getPage() instanceof $class) {
            return $this->parent;
        }

        return $this->parent->getParentOfClass($class);
    }

    /**
     * @return NodeMenuItem[]
     */
    public function getParents()
    {
        $parent = $this->getParent();
        $parents = array();
        while ($parent !== null) {
            $parents[] = $parent;
            $parent = $parent->getParent();
        }

        return array_reverse($parents);
    }

    /**
     * @param bool $includeHiddenFromNav Include hiddenFromNav nodes
     *
     * @return NodeMenuItem[]
     */
    public function getChildren($includeHiddenFromNav = true)
    {
        if (is_null($this->children)) {
            $children = $this->menu->getChildren($this->node, true);
            /* @var NodeMenuItem $child */
            foreach ($children as $child) {
                $child->setParent($this);
            }
            $this->children = $children;
        }

        $children = array_filter($this->children, function (NodeMenuItem $entry) use ($includeHiddenFromNav) {
            if ($entry->getNode()->isHiddenFromNav() && !$includeHiddenFromNav) {
                return false;
            }

            return true;
        });

        return $children;
    }

    /**
     * @param string $class
     *
     * @return NodeMenuItem[]
     */
    public function getChildrenOfClass($class)
    {
        // Check for namespace alias
        if (strpos($class, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $class);
            $class = $this->em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
        }
        $result = array();
        $children = $this->getChildren();
        foreach ($children as $child) {
            if ($child->getPage() instanceof $class) {
                $result[] = $child;
            }
        }

        return $result;
    }

    /**
     * Get the first child of class, this is not using the getChildrenOfClass method for performance reasons
     * @param string $class
     *
     * @return NodeMenuItem
     */
    public function getChildOfClass($class)
    {
        // Check for namespace alias
        if (strpos($class, ':') !== false) {
            list($namespaceAlias, $simpleClassName) = explode(':', $class);
            $class = $this->em->getConfiguration()->getEntityNamespace($namespaceAlias) . '\\' . $simpleClassName;
        }
        foreach ($this->getChildren() as $child) {
            if ($child->getPage() instanceof $class) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @return HasNodeInterface
     */
    public function getPage()
    {
        return $this->getNodeTranslation()->getPublicNodeVersion()->getRef($this->em);
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        return $this->menu->getActive($this->getSlug());
    }
}
