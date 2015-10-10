<?php

namespace Kunstmaan\MenuBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class MenuItemRepository extends NestedTreeRepository implements MenuItemRepositoryInterface
{

    /**
     * @param string $menuName
     * @param string $locale
     * @return array
     */
    public function getMenuItemsForLanguage($menuName, $locale)
    {
	$em = $this->getEntityManager();

	$query = $em
	    ->createQueryBuilder('mi')
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
	    ->andWhere('(nt.online = 1 OR nt.online IS NULL)')
	    ->andWhere('(n.deleted = 0 OR n.deleted IS NULL)')
	    ->getQuery();

	return $query->getArrayResult();
    }
}
