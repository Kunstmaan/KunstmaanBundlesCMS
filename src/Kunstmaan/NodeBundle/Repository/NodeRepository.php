<?php

namespace Kunstmaan\AdminNodeBundle\Repository;

use Kunstmaan\AdminBundle\Entity\User as Baseuser;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminBundle\Helper\ClassLookup;
use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;
use Kunstmaan\AdminNodeBundle\Entity\Node;

use Doctrine\ORM\EntityRepository;

/**
 * NodeRepository
 *
 */
class NodeRepository extends EntityRepository
{
    /**
     * @param string    $lang                 The locale
     * @param string    $permission           The permission (read, write, ...)
     * @param AclHelper $aclHelper
     * @param bool      $includeHiddenFromNav include the hiddenfromnav nodes or not
     *
     * @return Node[]
     */
    public function getTopNodes($lang, $permission, $aclHelper, $includeHiddenFromNav = false)
    {
        $result = $this->getChildNodes(null, $lang, $permission, $aclHelper, $includeHiddenFromNav);

        return $result;
    }

    /**
     * @param HasNodeInterface $hasNode
     *
     * @return Node|null
     */
    public function getNodeFor(HasNodeInterface $hasNode)
    {
        $nodeVersion = $this->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->getNodeVersionFor($hasNode);
        if (!is_null($nodeVersion)) {
            $nodeTranslation = $nodeVersion->getNodeTranslation();
            if (!is_null($nodeTranslation)) {
                return $nodeTranslation->getNode();
            }
        }

        return null;
    }

    /**
     * @param int    $id         The id
     * @param string $entityName The class name
     *
     * @return Node|null
     */
    public function getNodeForIdAndEntityname($id, $entityName)
    {
        $nodeVersion = $this->getEntityManager()->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->findOneBy(array('refId' => $id, 'refEntityName' => $entityName));
        if ($nodeVersion) {
            return $nodeVersion->getNodeTranslation()->getNode();
        }

        return null;
    }

    /**
     * @param Node   $parentNode The parent node (may be null)
     * @param string $slug       The slug
     *
     * @return Node|null
     */
    public function getNodeForSlug($parentNode, $slug)
    {
        $slugParts = explode("/", $slug);
        $result = null;
        foreach ($slugParts as $slugPart) {
            if ($parentNode) {
                if ($r = $this->findOneBy(array('slug' => $slugPart, 'parent.parent' => $parentNode->getId()))) {
                    $result = $r;
                }
            } else {
                if ($r = $this->findOneBy(array('slug' => $slugPart))) {
                    $result = $r;
                }
            }
        }

        return $result;
    }

    /**
     * @param HasNodeInterface $hasNode      The object to link to
     * @param string           $lang         The locale
     * @param Baseuser         $owner        The user
     * @param string           $internalName The internal name (may be null)
     *
     * @throws \InvalidArgumentException
     *
     * @return Node
     */
    public function createNodeFor(HasNodeInterface $hasNode, $lang, Baseuser $owner, $internalName = null)
    {
        $em = $this->getEntityManager();
        $node = new Node();
        $node->setRef($hasNode);
        if (!$hasNode->getId() > 0) {
            throw new \InvalidArgumentException("the entity of class " . $node->getRefEntityName() . " has no id, maybe you forgot to flush first");
        }
        $node->setDeleted(false);
        $node->setInternalName($internalName);
        $parent = $hasNode->getParent();
        if ($parent) {
            $parentNodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->findOneBy(array('refId' => $parent->getId(), 'refEntityName' => ClassLookup::getClass($parent)));
            if ($parentNodeVersion) {
                $node->setParent($parentNodeVersion->getNodeTranslation()->getNode());
            }
        }
        $em->persist($node);
        $em->flush();
        $em->refresh($node);
        $nodeTranslation = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($hasNode, $lang, $node, $owner);

        return $node;
    }

    /**
     * @param int       $parentId             The parent id
     * @param string    $lang                 The locale
     * @param string    $permission           The permission (read, write, ...)
     * @param AclHelper $aclHelper
     * @param bool      $includeHiddenFromNav Include nodes hidden from navigation or not
     *
     * @return Node[]
     */
    public function getChildNodes($parentId, $lang, $permission, AclHelper $aclHelper, $includeHiddenFromNav = false)
    {
        $qb = $this->createQueryBuilder('b')
                   ->select('b')
                   ->innerJoin('b.nodeTranslations', 't')
                   ->where('b.deleted = 0');

        if (!$includeHiddenFromNav) {
            $qb->andWhere('b.hiddenFromNav != true');
        }
        $qb->andWhere('t.lang = :lang');

        if (is_null($parentId)) {
            $qb->andWhere('b.parent is NULL');
        } else {
            $qb->andWhere('b.parent = :parent')
               ->setParameter('parent', $parentId);
        }
        $qb->addOrderBy('t.weight', 'ASC')
           ->addOrderBy('t.title', 'ASC');
        $qb->setParameter('lang', $lang);
        $query = $aclHelper->apply($qb, new PermissionDefinition(array($permission)));

        return $query->getResult();
    }

    /**
     * @return Node[]
     */
    public function getAllTopNodes()
    {
        $qb = $this->createQueryBuilder('b')
                   ->select('b')
                   ->where('b.deleted = 0')
                   ->andWhere('b.parent IS NULL');

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
