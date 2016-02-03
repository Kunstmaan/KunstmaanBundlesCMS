<?php

namespace Kunstmaan\NodeBundle\Helper\Menu;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclNativeHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Helper\NodeMenuItem;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Symfony\Component\HttpFoundation\Request;

/**
 * The Page Menu Adaptor
 */
class PageMenuAdaptor implements MenuAdaptorInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var AclNativeHelper
     */
    private $aclNativeHelper;

    /**
     * @var array
     */
    private $treeNodes = null;

    /**
     * @var array
     */
    private $activeNodeIds = null;

    /**
     * @var PagesConfiguration
     */
    private $pagesConfiguration;

    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param EntityManagerInterface       $em              The entity manager
     * @param AclNativeHelper              $aclNativeHelper The acl helper
     * @param PagesConfiguration           $pagesConfiguration
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(
        EntityManagerInterface $em,
        AclNativeHelper $aclNativeHelper,
        PagesConfiguration $pagesConfiguration,
        DomainConfigurationInterface $domainConfiguration
    ) {
        $this->em                  = $em;
        $this->aclNativeHelper     = $aclNativeHelper;
        $this->pagesConfiguration  = $pagesConfiguration;
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * In this method you can add children for a specific parent, but also
     * remove and change the already created children
     *
     * @param MenuBuilder $menu      The menu builder
     * @param MenuItem[]  &$children The children array that may be adapted
     * @param MenuItem    $parent    The parent menu item
     * @param Request     $request   The request
     */
    public function adaptChildren(
        MenuBuilder $menu,
        array &$children,
        MenuItem $parent = null,
        Request $request = null
    ) {
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('KunstmaanNodeBundle_nodes')
                ->setUniqueId('pages')
                ->setLabel('Pages')
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        } elseif (stripos($request->attributes->get('_route'), 'KunstmaanNodeBundle_nodes') === 0) {
            $treeNodes     = $this->getTreeNodes(
                $request->getLocale(),
                PermissionMap::PERMISSION_EDIT,
                $this->aclNativeHelper,
                true
            );
            $activeNodeIds = $this->getActiveNodeIds($request);

            if ('KunstmaanNodeBundle_nodes' == $parent->getRoute() && isset($treeNodes[0])) {
                $this->processNodes(
                    $menu,
                    $children,
                    $treeNodes[0],
                    $parent,
                    $activeNodeIds
                );
            } elseif ('KunstmaanNodeBundle_nodes_edit' == $parent->getRoute()) {
                $parentRouteParams = $parent->getRouteparams();
                $parent_id         = $parentRouteParams['id'];
                if (array_key_exists($parent_id, $treeNodes)) {
                    $this->processNodes(
                        $menu,
                        $children,
                        $treeNodes[$parent_id],
                        $parent,
                        $activeNodeIds
                    );
                }
            }
        }
    }

    /**
     * Get the list of nodes that is used in the admin menu.
     *
     * @param string          $lang
     * @param string          $permission
     * @param AclNativeHelper $aclNativeHelper
     * @param bool            $includeHiddenFromNav
     *
     * @return array
     */
    private function getTreeNodes(
        $lang,
        $permission,
        AclNativeHelper $aclNativeHelper,
        $includeHiddenFromNav
    ) {
        if (is_null($this->treeNodes)) {
            $repo            = $this->em->getRepository('KunstmaanNodeBundle:Node');
            $this->treeNodes = array();

            $rootNode = $this->domainConfiguration->getRootNode();

            // Get all nodes that should be shown in the menu
            $allNodes = $repo->getAllMenuNodes(
                $lang,
                $permission,
                $aclNativeHelper,
                $includeHiddenFromNav,
                $rootNode
            );
            /** @var Node $nodeInfo */
            foreach ($allNodes as $nodeInfo) {
                $refEntityName = $nodeInfo['ref_entity_name'];
                if ($this->pagesConfiguration->isHiddenFromTree($refEntityName)) {
                    continue;
                }
                $parent_id = is_null($nodeInfo['parent']) ? 0 : $nodeInfo['parent'];
                unset($nodeInfo['parent']);
                $this->treeNodes[$parent_id][] = $nodeInfo;
            }
            unset($allNodes);
        }

        return $this->treeNodes;
    }

    /**
     * Get an array with the id's off all nodes in the tree that should be
     * expanded.
     *
     * @param $request
     *
     * @return array
     */
    private function getActiveNodeIds($request)
    {
        if (is_null($this->activeNodeIds)) {
            if (stripos($request->attributes->get('_route'), 'KunstmaanNodeBundle_nodes_edit') === 0) {
                $repo = $this->em->getRepository('KunstmaanNodeBundle:Node');

                $currentNode         = $repo->findOneById($request->attributes->get('id'));
                $parentNodes         = $repo->getAllParents($currentNode);
                $this->activeNodeIds = array();
                foreach ($parentNodes as $parentNode) {
                    $this->activeNodeIds[] = $parentNode->getId();
                }
            }
        }

        return (is_null($this->activeNodeIds) ? array() : $this->activeNodeIds);
    }

    /**
     * @param MenuBuilder    $menu          The menu builder
     * @param MenuItem[]     &$children     The children array that may be
     *                                      adapted
     * @param NodeMenuItem[] $nodes         The nodes
     * @param MenuItem       $parent        The parent menu item
     * @param array          $activeNodeIds List with id's of all nodes that
     *                                      should be expanded in the tree
     */
    private function processNodes(
        MenuBuilder $menu,
        array &$children,
        array $nodes,
        MenuItem $parent = null,
        array $activeNodeIds
    ) {
        foreach ($nodes as $child) {
            $menuItem = new MenuItem($menu);
            $refName  = $child['ref_entity_name'];

            $menuItem
                ->setRoute('KunstmaanNodeBundle_nodes_edit')
                ->setRouteparams(array('id' => $child['id']))
                ->setUniqueId('node-' . $child['id'])
                ->setLabel($child['title'])
                ->setParent($parent)
                ->setOffline(!$child['online'] && !$this->pagesConfiguration->isStructureNode($refName))
                ->setFolder($this->pagesConfiguration->isStructureNode($refName))
                ->setRole('page')
                ->setWeight($child['weight'])
                ->addAttributes(
                    [
                        'page' => [
                            'class'    => $refName,
                            'children' => $this->pagesConfiguration->getPossibleChildTypes($refName),
                            'icon'     => $this->pagesConfiguration->getIcon($refName)
                        ]
                    ]
                );

            if (in_array($child['id'], $activeNodeIds)) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}
