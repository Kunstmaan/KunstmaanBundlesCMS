<?php

namespace Kunstmaan\MenuBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\MenuBundle\Entity\Menu;
use Kunstmaan\MenuBundle\Entity\MenuItem;

class MenuItemAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var Menu
     */
    private $menu;

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null, Menu $menu)
    {
        parent::__construct($em, $aclHelper);

        $this->setListTemplate('@KunstmaanMenu/AdminList/list-menu-item.html.twig');
        $this->setAddTemplate('@KunstmaanMenu/AdminList/edit-menu-item.html.twig');
        $this->setEditTemplate('@KunstmaanMenu/AdminList/edit-menu-item.html.twig');
        $this->menu = $menu;
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('title', 'kuma_menu.menu_item.adminlist.field.title', false, '@KunstmaanMenu/AdminList/menu-item-title.html.twig');
        $this->addField('online', 'kuma_menu.menu_item.adminlist.field.online', false, '@KunstmaanMenu/AdminList/menu-item-online.html.twig');
        $this->addField('type', 'kuma_menu.menu_item.adminlist.field.type', false);
        $this->addField('url', 'kuma_menu.menu_item.adminlist.field.url', false, '@KunstmaanMenu/AdminList/menu-item-url.html.twig');
        $this->addField('newWindow', 'kuma_menu.menu_item.adminlist.field.new_window', false);
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanMenuBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'MenuItem';
    }

    public function adaptQueryBuilder(QueryBuilder $qb)
    {
        $qb->andWhere('b.menu = :menu');
        $qb->setParameter('menu', $this->menu);
        $qb->orderBy('b.lft', 'ASC');
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return mixed
     */
    public function getValue($item, $columnName)
    {
        if ($columnName == 'title') {
            return $item->getDisplayTitle();
        }

        if ($columnName == 'online') {
            return $item;
        }

        if ($columnName == 'type') {
            if ($item->getType() == MenuItem::TYPE_PAGE_LINK) {
                return 'Page link';
            }

            return 'External link';
        }

        if ($columnName == 'url') {
            return $item;
        }

        return parent::getValue($item, $columnName);
    }

    /**
     * Return extra parameters for use in list actions.
     *
     * @return array
     */
    public function getExtraParameters()
    {
        return ['menuid' => $this->menu->getId()];
    }

    /**
     * You can override this method to do some custom things you need to do when adding an entity
     *
     * @param object $entity
     *
     * @return mixed
     */
    public function decorateNewEntity($entity)
    {
        $entity->setMenu($this->menu);

        return $entity;
    }

    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return 1000;
    }
}
