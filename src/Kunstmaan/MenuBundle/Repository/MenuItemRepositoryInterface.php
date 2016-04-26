<?php
/**
 * Created by PhpStorm.
 * User: bart
 * Date: 09/10/15
 * Time: 14:38
 */

namespace Kunstmaan\MenuBundle\Repository;


interface MenuItemRepositoryInterface
{
    /**
     * @param string $menuName
     * @param string $locale
     * @return array
     */
    public function getMenuItemsForLanguage($menuName, $locale);

}
