<?php

namespace Kunstmaan\MenuBundle\Repository;

use Gedmo\Tool\Wrapper\EntityWrapper;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use InvalidArgumentException;
use Kunstmaan\MenuBundle\Entity\BaseMenuItem;

class MenuItemRepository extends NestedTreeRepository implements MenuItemRepositoryInterface
{

    /**
     * @param string $menuName
     * @param string $locale
     * @return array
     */
    public function getMenuItemsForLanguage($menuName, $locale)
    {
        $query = $this->createQueryBuilder('mi')
            ->select('mi, nt, p')
            ->innerJoin('mi.menu', 'm')
            ->leftJoin('mi.parent', 'p')
            ->leftJoin('mi.nodeTranslation', 'nt')
            ->leftJoin('nt.node', 'n')
            ->orderBy('mi.lft', 'ASC')
            ->where('m.locale = :locale')
            ->setParameter('locale', $locale)
            ->andWhere('m.name = :name')
            ->setParameter('name', $menuName)
            ->andWhere('nt.online = 1 OR mi.type = :url_type')
            ->setParameter('url_type', BaseMenuItem::TYPE_URL_LINK);

        $query = $query->getQuery();
        
        return $query->getArrayResult();
    }


    /**
     * Get the query builder for next siblings of the given $node
     *
     * @param object $node
     * @param bool   $includeSelf - include the node itself
     *
     * @throws \Gedmo\Exception\InvalidArgumentException - if input is invalid
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getNextSiblingsQueryBuilder($node, $includeSelf = false)
    {
        $meta = $this->getClassMetadata();
        if (!$node instanceof $meta->name) {
            throw new InvalidArgumentException("Node is not related to this repository");
        }
        $wrapped = new EntityWrapper($node, $this->_em);
        if (!$wrapped->hasValidIdentifier()) {
            throw new InvalidArgumentException("Node is not managed by UnitOfWork");
        }

        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $parent = $wrapped->getPropertyValue($config['parent']);

        $left = $wrapped->getPropertyValue($config['left']);

        $qb = $this->getQueryBuilder();
        $qb->select('node')
            ->from($config['useObjectClass'], 'node')
            ->where($includeSelf ?
                $qb->expr()->gte('node.'.$config['left'], $left) :
                $qb->expr()->gt('node.'.$config['left'], $left)
            )
            ->orderBy("node.{$config['left']}", 'ASC')
        ;
        if ($parent) {
            $wrappedParent = new EntityWrapper($parent, $this->_em);
            $qb->andWhere($qb->expr()->eq('node.'.$config['parent'], ':pid'));
            $qb->setParameter('pid', $wrappedParent->getIdentifier());
        } else if (isset($config['root']) && !$parent) {
            $qb->andWhere($qb->expr()->eq('node.'.$config['root'], ':menu'));
            $qb->andWhere($qb->expr()->isNull('node.parent'));
            $qb->setParameter('menu', $node->getMenu());
        } else {
            $qb->andWhere($qb->expr()->isNull('node.'.$config['parent']));
        }

        return $qb;
    }

    /**
     * Get query builder for previous siblings of the given $node
     *
     * @param object $node
     * @param bool   $includeSelf - include the node itself
     *
     * @throws \Gedmo\Exception\InvalidArgumentException - if input is invalid
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getPrevSiblingsQueryBuilder($node, $includeSelf = false)
    {
        $meta = $this->getClassMetadata();
        if (!$node instanceof $meta->name) {
            throw new InvalidArgumentException("Node is not related to this repository");
        }
        $wrapped = new EntityWrapper($node, $this->_em);
        if (!$wrapped->hasValidIdentifier()) {
            throw new InvalidArgumentException("Node is not managed by UnitOfWork");
        }

        $config = $this->listener->getConfiguration($this->_em, $meta->name);
        $parent = $wrapped->getPropertyValue($config['parent']);

        $left = $wrapped->getPropertyValue($config['left']);

        $qb = $this->getQueryBuilder();
        $qb->select('node')
            ->from($config['useObjectClass'], 'node')
            ->where($includeSelf ?
                $qb->expr()->lte('node.'.$config['left'], $left) :
                $qb->expr()->lt('node.'.$config['left'], $left)
            )
            ->orderBy("node.{$config['left']}", 'ASC')
        ;
        if ($parent) {
            $wrappedParent = new EntityWrapper($parent, $this->_em);
            $qb->andWhere($qb->expr()->eq('node.'.$config['parent'], ':pid'));
            $qb->setParameter('pid', $wrappedParent->getIdentifier());
        } else if (isset($config['root']) && !$parent) {
            $qb->andWhere($qb->expr()->eq('node.'.$config['root'], ':menu'));
            $qb->andWhere($qb->expr()->isNull('node.parent'));
            $qb->setParameter('menu', $node->getMenu());
        } else {
            $qb->andWhere($qb->expr()->isNull('node.'.$config['parent']));
        }

        return $qb;
    }
}
