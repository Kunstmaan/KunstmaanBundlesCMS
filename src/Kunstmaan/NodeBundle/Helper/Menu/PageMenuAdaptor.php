<?php

namespace Kunstmaan\NodeBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\NodeMenuItem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Doctrine\ORM\EntityManager;

/**
 * The Page Menu Adaptor
 */
class PageMenuAdaptor implements MenuAdaptorInterface
{
    private $em;
    private $securityContext;
    private $aclHelper;
    private $nodeMenu;

    /**
     * @param EntityManager            $em              The entity manager
     * @param SecurityContextInterface $securityContext The security context
     * @param AclHelper                $aclHelper       The acl helper
     */
    public function __construct(EntityManager $em, SecurityContextInterface $securityContext, AclHelper $aclHelper)
    {
        $this->em              = $em;
        $this->securityContext = $securityContext;
        $this->aclHelper       = $aclHelper;
    }

    /**
     * In this method you can add children for a specific parent, but also remove and change the already created children
     *
     * @param MenuBuilder $menu      The menu builder
     * @param MenuItem[]  &$children The children array that may be adapted
     * @param MenuItem    $parent    The parent menu item
     * @param Request     $request   The request
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($this->nodeMenu)) {
            /* @var Node $node */
            $node = null;
            if ($request->attributes->get('_route') == 'KunstmaanNodeBundle_nodes_edit') {
                $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->findOneById($request->attributes->get('id'));
            }
            $this->nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $request->getLocale(), $node, PermissionMap::PERMISSION_EDIT, true, true);
        }
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem->setRoute('KunstmaanNodeBundle_nodes');
            $menuItem->setInternalName("Pages");
            $menuItem->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        } else {
            if ('KunstmaanNodeBundle_nodes' == $parent->getRoute()) {
                $topNodes = $this->nodeMenu->getTopNodes();
                $currentId = $request->attributes->get('id');
                $this->processNodes($currentId, $menu, $children, $topNodes, $parent, $request);
            } elseif ('KunstmaanNodeBundle_nodes_edit' == $parent->getRoute()) {
                $parentRouteParams = $parent->getRouteparams();
                /* @var Node $node */
                $node = $this->em->getRepository('KunstmaanNodeBundle:Node')->findOneById($parentRouteParams['id']);
                $nodeMenu = new NodeMenu($this->em, $this->securityContext, $this->aclHelper, $request->getLocale(), $node, PermissionMap::PERMISSION_EDIT, true, true);
                $childNodes = $nodeMenu->getCurrent()->getChildren();
                $currentId = $request->attributes->get('id');
                $this->processNodes($currentId, $menu, $children, $childNodes, $parent, $request);
            }
        }
    }

    /**
     * @param int            $currentId The current id
     * @param MenuBuilder    $menu      The menu builder
     * @param MenuItem[]     &$children The children array that may be adapted
     * @param NodeMenuItem[] $nodes     The nodes
     * @param MenuItem       $parent    The parent menu item
     * @param Request        $request   The request
     */
    private function processNodes($currentId, MenuBuilder $menu, array &$children, array $nodes, MenuItem $parent = null, Request $request = null)
    {
        if (isset($currentId)) {
            /* @var Node $currentNode */
            $currentNode = $this->em->getRepository('KunstmaanNodeBundle:Node')->findOneById($currentId);
            if (!is_null($currentNode)) {
                $parentNodes = $currentNode->getParents();
            } else {
                $parentNodes = array();
            }
        }

        foreach ($nodes as $child) {
            $menuItem = new MenuItem($menu);
            $menuItem->setRoute('KunstmaanNodeBundle_nodes_edit');
            $menuItem->setRouteparams(array('id' => $child->getId()));
            $menuItem->setInternalName($child->getTitle());
            $menuItem->setParent($parent);
            $menuItem->setOffline(!$child->getNodeTranslation()->isOnline());
            $menuItem->setRole('page');
            $menuItem->setWeight($child->getNodeTranslation()->getWeight());

            if (isset($currentNode) && stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                if ($currentNode->getId() == $child->getId()) {
                    $menuItem->setActive(true);
                } else if (isset($parentNodes)) {
                    foreach ($parentNodes as $parentNode) {
                        if ($parentNode->getId() == $child->getId()) {
                            $menuItem->setActive(true);
                            break;
                        }
                    }
                }
            }
            $children[] = $menuItem;
        }
    }

}
