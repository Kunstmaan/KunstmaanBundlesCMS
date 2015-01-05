<?php

namespace Kunstmaan\MediaBundle\Repository;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kunstmaan\MediaBundle\Entity\Folder;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * FolderRepository
 *
 * @method FolderRepository persistAsFirstChild(object $node)
 * @method FolderRepository persistAsFirstChildOf(object $node, object $parent)
 * @method FolderRepository persistAsLastChild(object $node)
 * @method FolderRepository persistAsLastChildOf(object $node, object $parent)
 * @method FolderRepository persistAsNextSibling(object $node)
 * @method FolderRepository persistAsNextSiblingOf(object $node, object $sibling)
 * @method FolderRepository persistAsPrevSibling(object $node)
 * @method FolderRepository persistAsPrevSiblingOf(object $node, object $sibling)
 */
class FolderRepository extends NestedTreeRepository
{
    /**
     * @param Folder $folder The folder
     *
     * @throws \Exception
     */
    public function save(Folder $folder)
    {
        $em = $this->getEntityManager();
        $parent = $folder->getParent();

        $em->beginTransaction();
        try {
            // Find where to insert the new item
            $children = $parent->getChildren(true);
            if ($children->isEmpty()) {
                // No children yet - insert as first child
                $this->persistAsFirstChildOf($folder, $parent);
            } else {
                $previousChild = null;
                foreach ($children as $child) {
                    // Alphabetical sorting - could be nice if we implemented a sorting strategy
                    if (strcasecmp($folder->getName(), $child->getName()) < 0) {
                        break;
                    }
                    $previousChild = $child;
                }
                if (is_null($previousChild)) {
                    $this->persistAsPrevSiblingOf($folder, $children[0]);
                } else {
                    $this->persistAsNextSiblingOf($folder, $previousChild);
                }
            }
            $em->commit();
            $em->flush();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }
    }

    /**
     * @param Folder $folder
     */
    public function delete(Folder $folder)
    {
        $em = $this->getEntityManager();

        $this->deleteMedia($folder);
        $this->deleteChildren($folder);
        $folder->setDeleted(true);
        $em->persist($folder);
        $em->flush();
    }

    /**
     * @param Folder $folder
     */
    private function deleteMedia(Folder $folder)
    {
        $em = $this->getEntityManager();

        /** @var Media $media */
        foreach ($folder->getMedia() as $media) {
            $media->setDeleted(true);
            $em->persist($media);
        }
    }

    /**
     * @param Folder $folder
     */
    private function deleteChildren(Folder $folder)
    {
        $em = $this->getEntityManager();

        /** @var Folder $child */
        foreach ($folder->getChildren() as $child) {
            $this->deleteMedia($child);
            $this->deleteChildren($child);
            $child->setDeleted(true);
            $em->persist($child);
        }
    }

    /**
     * @param int $limit
     *
     * @return array
     */
    public function getAllFolders($limit = null)
    {
        $qb = $this->createQueryBuilder('folder')
            ->select('folder')
            ->where('folder.parent is null AND folder.deleted != true')
            ->orderBy('folder.name');

        if (false === is_null($limit)) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $folderId
     *
     * @return object
     * @throws EntityNotFoundException
     */
    public function getFolder($folderId)
    {
        $folder = $this->find($folderId);
        if (!$folder) {
            throw new EntityNotFoundException();
        }

        return $folder;
    }

    public function getFirstTopFolder()
    {
        $folder = $this->findOneBy(array('parent' => null));
        if (!$folder) {
            throw new EntityNotFoundException();
        }

        return $folder;
    }

    public function getParentIds(Folder $folder)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->getPathQueryBuilder($folder)
            ->select('node.id');

        $result = $qb->getQuery()->getScalarResult();
        $ids = array_map('current', $result);

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function getPathQueryBuilder($node)
    {
        /** @var QueryBuilder $qb */
        $qb = parent::getPathQueryBuilder($node);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getRootNodesQueryBuilder($sortByField = null, $direction = 'asc')
    {
        /** @var QueryBuilder $qb */
        $qb = parent::getRootNodesQueryBuilder($sortByField, $direction);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function childrenQueryBuilder(
        $node = null,
        $direct = false,
        $sortByField = null,
        $direction = 'ASC',
        $includeNode = false
    )
    {
        /** @var QueryBuilder $qb */
        $qb = parent::childrenQueryBuilder($node, $direct, $sortByField, $direction, $includeNode);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getLeafsQueryBuilder($root = null, $sortByField = null, $direction = 'ASC')
    {
        /** @var QueryBuilder $qb */
        $qb = parent::getLeafsQueryBuilder($root, $sortByField, $direction);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextSiblingsQueryBuilder($node, $includeSelf = false)
    {
        /** @var QueryBuilder $qb */
        $qb = parent::getNextSiblingsQueryBuilder($node, $includeSelf);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrevSiblingsQueryBuilder($node, $includeSelf = false)
    {
        /** @var QueryBuilder $qb */
        $qb = parent::getPrevSiblingsQueryBuilder($node, $includeSelf);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodesHierarchyQueryBuilder(
        $node = null,
        $direct = false,
        array $options = array(),
        $includeNode = false
    )
    {
        /** @var QueryBuilder $qb */
        $qb = parent::getNodesHierarchyQueryBuilder($node, $direct, $options, $includeNode);
        $qb->andWhere('node.deleted != true');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodesHierarchy($node = null, $direct = false, array $options = array(), $includeNode = false)
    {
        $query = $this->getNodesHierarchyQuery($node, $direct, $options, $includeNode);
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );

        return $query->getArrayResult();
    }

    /**
     * Rebuild the nested tree
     */
    public function rebuildTree()
    {
        $em = $this->getEntityManager();

        // Reset tree...
        $sql = 'UPDATE kuma_folders SET lvl=NULL,lft=NULL,rgt=NULL';
        $stmt = $em->getConnection()->prepare($sql);
        $stmt->execute();

        $folders = $this->findBy(array(), array('parent' => 'ASC', 'name' => 'asc'));

        $rootFolder = $folders[0];
        $first = true;
        foreach ($folders as $folder) {
            // Force parent load
            $parent = $folder->getParent();
            if (is_null($parent)) {
                $folder->setLevel(0);
                if ($first) {
                    $this->persistAsFirstChild($folder);
                    $first = false;
                } else {
                    $this->persistAsNextSiblingOf($folder, $rootFolder);
                }
            } else {
                $folder->setLevel($parent->getLevel() + 1);
                $this->persistAsLastChildOf($folder, $parent);
            }
        }
        $em->flush();
    }

    /**
     * Used as querybuilder for Folder entity selectors
     *
     * @param Folder $ignoreSubtree Folder (with children) that has to be filtered out (optional)
     *
     * @return QueryBuilder
     */
    public function selectFolderQueryBuilder(Folder $ignoreSubtree = null)
    {
        /** @var QueryBuilder $qb */
        $qb = $this->createQueryBuilder('f');
        $qb->where('f.deleted != true')
            ->orderBy('f.lft');

        // Fetch all folders except the current one and its children
        if (!is_null($ignoreSubtree) && $ignoreSubtree->getId() !== null) {
            $orX = $qb->expr()->orX();
            $orX->add('f.rgt > :right')
                ->add('f.lft < :left');

            $qb->andWhere($orX)
                ->setParameter('left', $ignoreSubtree->getLeft())
                ->setParameter('right', $ignoreSubtree->getRight());
        }

        return $qb;
    }
}
