<?php

namespace Kunstmaan\NodeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * NodeRepository
 */
class NodeTranslationRepository extends EntityRepository
{
    /**
     * Get all childs of a given node
     *
     * @param Node $node
     *
     * @return array
     */
    public function getChildren(Node $node)
    {
        return $this->findBy(array('parent' => $node->getId()), array('weight' => 'ASC'));
    }

    /**
     * QueryBuilder to fetch node translations (ignoring nodes that have been deleted)
     *
     * @param string $lang (optional) Only return NodeTranslations for the given language
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getNodeTranslationsQueryBuilder($lang = null)
    {
        $queryBuilder = $this->createQueryBuilder('nt')
            ->select('nt,n,v')
            ->innerJoin('nt.node', 'n')
            ->leftJoin('nt.publicNodeVersion', 'v', 'WITH', 'nt.publicNodeVersion = v.id')
            ->where('n.deleted = false')
            ->orderBy('nt.weight')
            ->addOrderBy('nt.weight');

        if (!empty($lang)) {
            $queryBuilder
                ->andWhere('nt.lang = :lang')
                ->setParameter('lang', $lang);
        }

        return $queryBuilder;
    }

    /**
     * QueryBuilder to fetch node translations that are currently published (ignoring nodes that have been deleted)
     *
     * @param string $lang (optional) Only return NodeTranslations for the given language
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOnlineNodeTranslationsQueryBuilder($lang = null)
    {
        return $this->getNodeTranslationsQueryBuilder($lang)
            ->andWhere('nt.online = true');
    }

    /**
     * QueryBuilder to fetch immediate child NodeTranslations for a specific node and (optional) language
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getChildrenQueryBuilder(Node $parent, $lang = null)
    {
        return $this->getNodeTranslationsQueryBuilder($lang)
            ->andWhere('n.parent = :parent')
            ->setParameter('parent', $parent);
    }

    /**
     * QueryBuilder to fetch immediate child NodeTranslations for a specific node and (optional) language
     * that are currently published
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getOnlineChildrenQueryBuilder(Node $parent, $lang = null)
    {
        return $this->getChildrenQueryBuilder($parent, $lang)
            ->andWhere('nt.online = true');
    }

    /**
     * Get all online child node translations for a given node and (optional) language
     *
     * @param Node   $parent
     * @param string $lang (optional, if not specified all languages will be returned)
     *
     * @return array
     */
    public function getOnlineChildren(Node $parent, $lang = null)
    {
        return $this->getOnlineChildrenQueryBuilder($parent, $lang)
            ->getQuery()->getResult();
    }

    /**
     * @deprecated Use getOnlineNodeTranslationsQueryBuilder instead
     *
     * This returns the node translations that are currently published
     *
     * @return array
     */
    public function getOnlineNodes()
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'v')
            ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->leftJoin('b.publicNodeVersion', 'v', 'WITH', 'b.publicNodeVersion = v.id')
            ->where('n.deleted != 1 AND b.online = 1');
    }

    /**
     * Get the node translation for a node
     *
     * @param HasNodeInterface $hasNode
     *
     * @return NodeTranslation
     */
    public function getNodeTranslationFor(HasNodeInterface $hasNode)
    {
        /* @var NodeVersion $nodeVersion */
        $nodeVersion = $this->getEntityManager()
            ->getRepository('KunstmaanNodeBundle:NodeVersion')
            ->getNodeVersionFor($hasNode);

        if (!is_null($nodeVersion)) {
            return $nodeVersion->getNodeTranslation();
        }

        return null;
    }

    /**
     * Get the node translation for a given slug string
     *
     * @param string               $slug       The slug
     * @param NodeTranslation|null $parentNode The parentnode
     *
     * @return NodeTranslation|null
     */
    public function getNodeTranslationForSlug($slug, NodeTranslation $parentNode = null)
    {
        if (empty($slug)) {
            return $this->getNodeTranslationForSlugPart(null, $slug);
        }

        $slugParts = explode('/', $slug);
        $result    = $parentNode;
        foreach ($slugParts as $slugPart) {
            $result = $this->getNodeTranslationForSlugPart($result, $slugPart);
        }

        return $result;
    }

    /**
     * Returns the node translation for a given slug
     *
     * @param NodeTranslation|null $parentNode The parentNode
     * @param string               $slugPart   The slug part
     *
     * @return NodeTranslation|null
     */
    private function getNodeTranslationForSlugPart(NodeTranslation $parentNode = null, $slugPart = '')
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t', 'v', 'n')
            ->innerJoin('t.node', 'n', 'WITH', 't.node = n.id')
            ->leftJoin('t.publicNodeVersion', 'v', 'WITH', 't.publicNodeVersion = v.id')
            ->where('n.deleted != 1')
            ->addOrderBy('n.sequenceNumber', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        if ($parentNode !== null) {
            $qb->andWhere('t.slug = :slug')
                ->andWhere('n.parent = :parent')
                ->setParameter('slug', $slugPart)
                ->setParameter('parent', $parentNode->getNode()->getId());
        } else {
            /* if parent is null we should look for slugs that have no parent */
            $qb->andWhere('n.parent IS NULL');
            if (empty($slugPart)) {
                $qb->andWhere('t.slug is NULL');
            } else {
                $qb->andWhere('t.slug = :slug');
                $qb->setParameter('slug', $slugPart);
            }
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get the node translation for a given url
     *
     * @param string          $urlSlug        The full url
     * @param string          $locale         The locale
     * @param boolean         $includeDeleted Include deleted nodes
     * @param NodeTranslation $toExclude      Optional NodeTranslation instance you wish to exclude
     *
     * @return NodeTranslation|null
     */
    public function getNodeTranslationForUrl(
        $urlSlug,
        $locale = '',
        $includeDeleted = false,
        NodeTranslation $toExclude = null
    ) {
        $qb = $this->createQueryBuilder('b')
            ->select('b', 'v')
            ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->leftJoin('b.publicNodeVersion', 'v', 'WITH', 'b.publicNodeVersion = v.id')
            ->addOrderBy('b.online', 'DESC')
            ->addOrderBy('n.sequenceNumber', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        if (!$includeDeleted) {
            $qb->andWhere('n.deleted = 0');
        }

        if (!empty($locale)) {
            $qb->andWhere('b.lang = :lang')
                ->setParameter('lang', $locale);
        }

        if (empty($urlSlug)) {
            $qb->andWhere('b.url IS NULL');
        } else {
            $qb->andWhere('b.url = :url');
            $qb->setParameter('url', $urlSlug);
        }

        if (!is_null($toExclude)) {
            $qb->andWhere('NOT b.id = :exclude_id')
                ->setParameter('exclude_id', $toExclude->getId());
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Get all top node translations
     *
     * @return NodeTranslation[]
     */
    public function getTopNodeTranslations()
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b', 'v')
            ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->leftJoin('b.publicNodeVersion', 'v', 'WITH', 'b.publicNodeVersion = v.id')
            ->where('n.parent IS NULL')
            ->andWhere('n.deleted != 1');

        return $qb->getQuery()->getResult();
    }

    /**
     * Create a node translation for a given node
     *
     * @param HasNodeInterface $hasNode The hasNode
     * @param string           $lang    The locale
     * @param Node             $node    The node
     * @param BaseUser         $owner   The user
     *
     * @throws \InvalidArgumentException
     *
     * @return NodeTranslation
     */
    public function createNodeTranslationFor(HasNodeInterface $hasNode, $lang, Node $node, BaseUser $owner)
    {
        $em        = $this->getEntityManager();
        $className = ClassLookup::getClass($hasNode);
        if (!$hasNode->getId() > 0) {
            throw new \InvalidArgumentException("The entity of class " . $className .
                " has no id, maybe you forgot to flush first");
        }

        $nodeTranslation = new NodeTranslation();
        $nodeTranslation
            ->setNode($node)
            ->setLang($lang)
            ->setTitle($hasNode->getTitle())
            ->setOnline(false)
            ->setWeight(0);

        $em->persist($nodeTranslation);

        $nodeVersion = $em->getRepository('KunstmaanNodeBundle:NodeVersion')->createNodeVersionFor(
            $hasNode,
            $nodeTranslation,
            $owner,
            null
        );

        $nodeTranslation->setPublicNodeVersion($nodeVersion);
        $em->persist($nodeTranslation);
        $em->flush();
        $em->refresh($nodeTranslation);
        $em->refresh($node);

        return $nodeTranslation;
    }

    /**
     * Find best match for given URL and locale
     *
     * @param string $urlSlug The slug
     * @param string $locale  The locale
     *
     * @return NodeTranslation
     */
    public function getBestMatchForUrl($urlSlug, $locale)
    {
        $em = $this->getEntityManager();

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('Kunstmaan\NodeBundle\Entity\NodeTranslation', 'nt');

        $query = $em
            ->createNativeQuery(
                'select nt.*
                from kuma_node_translations nt
                join kuma_nodes n on n.id = nt.node_id
                where n.deleted = 0 and nt.lang = :lang and locate(nt.url, :url) = 1
                order by length(nt.url) desc limit 1',
                $rsm
            );
        $query->setParameter('lang', $locale);
        $query->setParameter('url', $urlSlug);
        $translation = $query->getOneOrNullResult();

        return $translation;
    }


    /**
     * Test if all parents of the specified NodeTranslation have a node translation for the specified language
     *
     * @param NodeTranslation $nodeTranslation The node translation
     * @param string          $language        The locale
     *
     * @return bool
     */
    public function hasParentNodeTranslationsForLanguage(NodeTranslation $nodeTranslation, $language)
    {
        $parentNode = $nodeTranslation->getNode()->getParent();
        if ($parentNode !== null) {
            $parentNodeTranslation = $parentNode->getNodeTranslation($language, true);
            if ($parentNodeTranslation !== null) {
                return $this->hasParentNodeTranslationsForLanguage($parentNodeTranslation, $language);
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * This will return 1 NodeTranslation by default (if one exists).
     * Just give it the internal name as defined on the Node in the database and the language.
     *
     * It'll only return the latest version. It'll also hide deleted & offline nodes.
     *
     * @param $language
     * @param $internalName
     */
    public function getNodeTranslationByLanguageAndInternalName($language, $internalName)
    {
        $qb = $this->createQueryBuilder('nt')
            ->select('nt', 'v')
            ->innerJoin('nt.node', 'n', 'WITH', 'nt.node = n.id')
            ->leftJoin('nt.publicNodeVersion', 'v', 'WITH', 'nt.publicNodeVersion = v.id')
            ->where('n.deleted != 1')
            ->andWhere('nt.online = 1')
            ->addOrderBy('n.sequenceNumber', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1);

        $qb->andWhere('nt.lang = :lang')
            ->setParameter('lang', $language);

        $qb->andWhere('n.internalName = :internal_name')
            ->setParameter('internal_name', $internalName);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getAllNodeTranslationsByRefEntityName($refEntityName)
    {
        $qb = $this->createQueryBuilder('nt')
            ->select('nt,n')
            ->innerJoin('nt.publicNodeVersion', 'nv')
            ->innerJoin('nt.node', 'n')
            ->where('nv.refEntityName = :refEntityName')
            ->setParameter('refEntityName', $refEntityName);

        return $qb->getQuery()->getResult();
    }
}
