<?php


namespace Kunstmaan\NodeBundle\Helper;


use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;

interface NodeMenuItemInterface
{
    /**
     * @param Node                    $node            The node
     * @param NodeTranslation         $nodeTranslation The nodetranslation
     * @param NodeMenuItem|null|false $parent          The parent nodemenuitem
     * @param NodeMenu                $menu            The menu
     */
    public function __construct(Node $node, NodeTranslation $nodeTranslation, $parent = false, NodeMenu $menu);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return Node
     */
    public function getNode();

    /**
     * @return NodeTranslation
     */
    public function getNodeTranslation();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return bool
     */
    public function getOnline();

    /**
     * @return string|null
     */
    public function getSlugPart();

    /**
     * @return string
     */
    public function getSlug();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return NodeMenuItem|null
     */
    public function getParent();

    /**
     * @param NodeMenuItem|null|false $parent
     */
    public function setParent($parent = false);

    /**
     * @param string $class
     *
     * @return NodeMenuItem|null
     */
    public function getParentOfClass($class);

    /**
     * @return NodeMenuItem[]
     */
    public function getParents();

    /**
     * @param bool $includeHiddenFromNav Include hiddenFromNav nodes
     *
     * @return NodeMenuItem[]
     */
    public function getChildren($includeHiddenFromNav = true);

    /**
     * @param string $class
     *
     * @return NodeMenuItem[]
     */
    public function getChildrenOfClass($class);

    /**
     * Get the first child of class, this is not using the getChildrenOfClass method for performance reasons
     *
     * @param string $class
     *
     * @return NodeMenuItem
     */
    public function getChildOfClass($class);

    /**
     * @return HasNodeInterface
     */
    public function getPage();

    /**
     * @return bool
     */
    public function getActive();

    /**
     * @return string
     */
    public function getLang();
}
