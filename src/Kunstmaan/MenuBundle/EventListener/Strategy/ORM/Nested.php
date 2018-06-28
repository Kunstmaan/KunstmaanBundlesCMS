<?php

namespace Kunstmaan\MenuBundle\EventListener\Strategy\ORM;

use Doctrine\ORM\EntityManager;
use Gedmo\Exception\UnexpectedValueException;
use Gedmo\Tool\Wrapper\AbstractWrapper;
use Gedmo\Tree\Strategy\ORM\Nested as GedmoNested;

class Nested extends GedmoNested
{
    private $nodePositions;
    private $delayedNodes;
    private $treeEdges;

    /**
     * Update the $node with a diferent $parent
     * destination
     *
     * @param EntityManager $em
     * @param object        $node     - target node
     * @param object        $parent   - destination node
     * @param string        $position
     *
     * @throws \Gedmo\Exception\UnexpectedValueException
     */
    public function updateNode(EntityManager $em, $node, $parent, $position = 'FirstChild')
    {
        $wrapped = AbstractWrapper::wrap($node, $em);

        /** @var ClassMetadata $meta */
        $meta = $wrapped->getMetadata();
        $config = $this->listener->getConfiguration($em, $meta->name);

        $root = isset($config['root']) ? $wrapped->getPropertyValue($config['root']) : null;
        $identifierField = $meta->getSingleIdentifierFieldName();
        $nodeId = $wrapped->getIdentifier();

        $left = $wrapped->getPropertyValue($config['left']);
        $right = $wrapped->getPropertyValue($config['right']);

        $isNewNode = empty($left) && empty($right);
        if ($isNewNode) {
            $left = 1;
            $right = 2;
        }

        $oid = spl_object_hash($node);
        if (isset($this->nodePositions[$oid])) {
            $position = $this->nodePositions[$oid];
        }
        $level = 0;
        $treeSize = $right - $left + 1;
        $newRoot = null;
        if ($parent) {
            $wrappedParent = AbstractWrapper::wrap($parent, $em);

            $parentRoot = isset($config['root']) ? $wrappedParent->getPropertyValue($config['root']) : null;
            $parentOid = spl_object_hash($parent);
            $parentLeft = $wrappedParent->getPropertyValue($config['left']);
            $parentRight = $wrappedParent->getPropertyValue($config['right']);
            if (empty($parentLeft) && empty($parentRight)) {
                // parent node is a new node, but wasn't processed yet (due to Doctrine commit order calculator redordering)
                // We delay processing of node to the moment parent node will be processed
                if (!isset($this->delayedNodes[$parentOid])) {
                    $this->delayedNodes[$parentOid] = array();
                }
                $this->delayedNodes[$parentOid][] = array('node' => $node, 'position' => $position);

                return;
            }
            if (!$isNewNode && $root === $parentRoot && $parentLeft >= $left && $parentRight <= $right) {
                throw new UnexpectedValueException("Cannot set child as parent to node: {$nodeId}");
            }
            if (isset($config['level'])) {
                $level = $wrappedParent->getPropertyValue($config['level']);
            }
            switch ($position) {
                case self::PREV_SIBLING:
                    if (property_exists($node, 'sibling')) {
                        $wrappedSibling = AbstractWrapper::wrap($node->sibling, $em);
                        $start = $wrappedSibling->getPropertyValue($config['left']);
                        $level++;
                    } else {
                        $newParent = $wrappedParent->getPropertyValue($config['parent']);
                        if ( (is_null($newParent) && (isset($config['root']) && $config['root'] == $config['parent']) || $isNewNode)) {
                            throw new UnexpectedValueException("Cannot persist sibling for a root node, tree operation is not possible");
                        } else if (is_null($newParent) && (isset($config['root']) || $isNewNode)) {
                            // root is a different column from parent (pointing to another table?), do nothing
                        } else {
                            $wrapped->setPropertyValue($config['parent'], $newParent);
                        }

                        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $node);
                        $start = $parentLeft;
                    }
                    break;

                case self::NEXT_SIBLING:
                    if (property_exists($node, 'sibling')) {
                        $wrappedSibling = AbstractWrapper::wrap($node->sibling, $em);
                        $start = $wrappedSibling->getPropertyValue($config['right']) + 1;
                        $level++;
                    } else {
                        $newParent = $wrappedParent->getPropertyValue($config['parent']);
                        if ( (is_null($newParent) && (isset($config['root']) && $config['root'] == $config['parent']) || $isNewNode)) {
                            throw new UnexpectedValueException("Cannot persist sibling for a root node, tree operation is not possible");
                        } else if (is_null($newParent) && (isset($config['root']) || $isNewNode)) {
                            // root is a different column from parent (pointing to another table?), do nothing
                        } else {
                            $wrapped->setPropertyValue($config['parent'], $newParent);
                        }
                        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $node);
                        $start = $parentRight + 1;
                    }
                    break;

                case self::LAST_CHILD:
                    $start = $parentRight;
                    $level++;
                    break;

                case self::FIRST_CHILD:
                default:
                    $start = $parentLeft + 1;
                    $level++;
                    break;
            }
            $this->shiftRL($em, $config['useObjectClass'], $start, $treeSize, $parentRoot);
            if (!$isNewNode && $root === $parentRoot && $left >= $start) {
                $left += $treeSize;
                $wrapped->setPropertyValue($config['left'], $left);
            }
            if (!$isNewNode && $root === $parentRoot && $right >= $start) {
                $right += $treeSize;
                $wrapped->setPropertyValue($config['right'], $right);
            }
            $newRoot = $parentRoot;
        } elseif (!isset($config['root']) ||
            ($meta->isSingleValuedAssociation($config['root']) && ($newRoot = $meta->getFieldValue($node, $config['root'])))) {

            if (!isset($this->treeEdges[$meta->name])) {
                $this->treeEdges[$meta->name] = $this->max($em, $config['useObjectClass'], $newRoot) + 1;
            }

            $level = 0;
            $parentLeft = 0;
            $parentRight = $this->treeEdges[$meta->name];
            $this->treeEdges[$meta->name] += 2;

            switch ($position) {
                case self::PREV_SIBLING:
                    if (property_exists($node, 'sibling')) {
                        $wrappedSibling = AbstractWrapper::wrap($node->sibling, $em);
                        $start = $wrappedSibling->getPropertyValue($config['left']);
                    } else {
                        $wrapped->setPropertyValue($config['parent'], null);
                        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $node);
                        $start = $parentLeft + 1;
                    }
                    break;

                case self::NEXT_SIBLING:
                    if (property_exists($node, 'sibling')) {
                        $wrappedSibling = AbstractWrapper::wrap($node->sibling, $em);
                        $start = $wrappedSibling->getPropertyValue($config['right']) + 1;
                    } else {
                        $wrapped->setPropertyValue($config['parent'], null);
                        $em->getUnitOfWork()->recomputeSingleEntityChangeSet($meta, $node);
                        $start = $parentRight;
                    }
                    break;

                case self::LAST_CHILD:
                    $start = $parentRight;
                    break;

                case self::FIRST_CHILD:
                default:
                    $start = $parentLeft + 1;
                    break;
            }

            $this->shiftRL($em, $config['useObjectClass'], $start, $treeSize, null);

            if (!$isNewNode && $left >= $start) {
                $left += $treeSize;
                $wrapped->setPropertyValue($config['left'], $left);
            }
            if (!$isNewNode && $right >= $start) {
                $right += $treeSize;
                $wrapped->setPropertyValue($config['right'], $right);
            }
        } else {
            $start = 1;

            if ($meta->isSingleValuedAssociation($config['root'])) {
                $newRoot = $node;
            } else {
                $newRoot = $wrapped->getIdentifier();
            }
        }

        $diff = $start - $left;

        if (!$isNewNode) {
            $levelDiff = isset($config['level']) ? $level - $wrapped->getPropertyValue($config['level']) : null;
            $this->shiftRangeRL(
                $em,
                $config['useObjectClass'],
                $left,
                $right,
                $diff,
                $root,
                $newRoot,
                $levelDiff
            );
            $this->shiftRL($em, $config['useObjectClass'], $left, -$treeSize, $root);
        } else {
            $qb = $em->createQueryBuilder();
            $qb->update($config['useObjectClass'], 'node');
            if (isset($config['root'])) {
                $qb->set('node.'.$config['root'], ':rid');
                $qb->setParameter('rid', $newRoot);
                $wrapped->setPropertyValue($config['root'], $newRoot);
                $em->getUnitOfWork()->setOriginalEntityProperty($oid, $config['root'], $newRoot);
            }
            if (isset($config['level'])) {
                $qb->set('node.'.$config['level'], $level);
                $wrapped->setPropertyValue($config['level'], $level);
                $em->getUnitOfWork()->setOriginalEntityProperty($oid, $config['level'], $level);
            }
            if (isset($newParent)) {
                $wrappedNewParent = AbstractWrapper::wrap($newParent, $em);
                $newParentId = $wrappedNewParent->getIdentifier();
                $qb->set('node.'.$config['parent'], ':pid');
                $qb->setParameter('pid', $newParentId);
                $wrapped->setPropertyValue($config['parent'], $newParent);
                $em->getUnitOfWork()->setOriginalEntityProperty($oid, $config['parent'], $newParent);
            }
            $qb->set('node.'.$config['left'], $left + $diff);
            $qb->set('node.'.$config['right'], $right + $diff);
            // node id cannot be null
            $qb->where($qb->expr()->eq('node.'.$identifierField, ':id'));
            $qb->setParameter('id', $nodeId);
            $qb->getQuery()->getSingleScalarResult();
            $wrapped->setPropertyValue($config['left'], $left + $diff);
            $wrapped->setPropertyValue($config['right'], $right + $diff);
            $em->getUnitOfWork()->setOriginalEntityProperty($oid, $config['left'], $left + $diff);
            $em->getUnitOfWork()->setOriginalEntityProperty($oid, $config['right'], $right + $diff);
        }
        if (isset($this->delayedNodes[$oid])) {
            foreach ($this->delayedNodes[$oid] as $nodeData) {
                $this->updateNode($em, $nodeData['node'], $node, $nodeData['position']);
            }
        }
    }
}