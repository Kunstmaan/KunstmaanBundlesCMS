<?php
namespace Kunstmaan\SearchBundle\Helper;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\SearchBundle\Entity\IndexableInterface;

/**
 * Logic for getting searchinfo out of nodes.
 */
class NodeContentSearcher
{

    /*
     * Get the node class and return it if it's indexable.
     */
    public function getSearchContentForNode($container, $entity, $field)
    {
        $page = $entity->getRef($container->get('doctrine')->getEntityManager());
        if ($page instanceof IndexableInterface) {
            return $page;
        }

        return null;
    }

    public function getParentsAndSelfForNode($container, $entity, $field)
    {
        $node    = $entity->getNode();
        $results = array();
        if ($node->getParent() == null) {
            $parents[] = $node->getId();
        } else {
            $parents = $this->getAllParentsForNode($node, $results);
        }
        return 'start ' . implode(' ', $parents) . ' stop';
    }

    /**
     * @param Node  $node
     * @param array $results
     *
     * @return array
     */
    public function getAllParentsForNode(Node $node, $results)
    {
        $parentNode = $node->getParent();
        if (is_object($parentNode)) {
            $results[] = $parentNode->getId();

            return $this->getAllParentsForNode($parentNode, $results);
        } else {
            return $results;
        }
    }

}

