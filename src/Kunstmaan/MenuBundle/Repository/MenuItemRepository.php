<?php

namespace Kunstmaan\MenuBundle\Repository;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Kunstmaan\MenuBundle\Entity\BaseMenuItem;

class MenuItemRepository extends NestedTreeRepository implements MenuItemRepositoryInterface
{
    /**
     * @param string $menuName
     * @param string $locale
     *
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
}
