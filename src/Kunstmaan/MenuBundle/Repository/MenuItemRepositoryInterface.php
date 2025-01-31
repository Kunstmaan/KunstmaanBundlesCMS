<?php

namespace Kunstmaan\MenuBundle\Repository;

interface MenuItemRepositoryInterface
{
    /**
     * @param string $menuName
     * @param string $locale
     *
     * @return array
     */
    public function getMenuItemsForLanguage($menuName, $locale);
}
