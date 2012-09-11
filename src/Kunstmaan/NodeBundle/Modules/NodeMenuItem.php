<?php

namespace Kunstmaan\AdminNodeBundle\Modules;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Symfony\Component\Translation\Translator;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;

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
     * @var string
     */
    private $lang;

    /**
     * @var NodeMenuItem[]
     */
    private $lazyChildren = null;

    /**
     * @var NodeMenuItem
     */
    private $parent;

    /**
     * @var NodeMenu
     */
    private $menu;

    /**
     * @param Node            $node            The node
     * @param NodeTranslation $nodeTranslation The nodetranslation
     * @param NodeMenuItem    $parent          The parent nodemenuitem
     * @param NodeMenu        $menu            The menu
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, $parent, NodeMenu $menu)
    {        
        $this->node = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->parent = $parent;
        $this->menu = $menu;
        $this->em = $menu->getEntityManager();
        $this->lang = $menu->getLang();
    }

    /**
     * @return integer
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
        return $this->lang;
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
     * @return boolean
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
        $result = $this->getSlugPart();

        return $result;
    }

    /**
     * @return NodeMenuItem|NULL
     */
    public function getParent()
    {
        return $this->parent;
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
        if ($this->parent == null) {
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
        $parents=array();
        while ($parent!=null) {
            $parents[] = $parent;
            $parent = $parent->getParent();
        }

        return array_reverse($parents);
    }

    /**
     * @param boolean $includehiddenfromnav
     *
     * @return NodeMenuItem[]
     */
    public function getChildren($includehiddenfromnav = true)
    {
        if (is_null($this->lazyChildren)) {
            $this->lazyChildren = array();
            $nodeRepo = $this->em->getRepository('KunstmaanAdminNodeBundle:Node');
            $children = $nodeRepo->getChildNodes($this->node->getId(), $this->lang, $this->menu->getPermission(), $this->menu->getAclHelper(), true);
            foreach ($children as $child) {
                $nodeTranslation = $child->getNodeTranslation($this->lang, $this->menu->isIncludeOffline());
                if (!is_null($nodeTranslation)) {
                    $this->lazyChildren[] = new NodeMenuItem($child, $nodeTranslation, $this, $this->menu);
                }
            }
        }

        return array_filter($this->lazyChildren, function ($entry) use ($includehiddenfromnav)
        {
            if ($entry->getNode()->isHiddenFromNav() && !$includehiddenfromnav) {
                return false;
            }

            return true;
        });
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
        return $this->getNodeTranslation()->getRef($this->em);
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        //TODO: change to something like in_array() but that didn't work
        $bc = $this->menu->getBreadCrumb();
        foreach ($bc as $bcItem) {
            if ($bcItem->getSlug() == $this->getSlug()) {
                return true;
            }
        }

        return false;
    }
}
