<?php

namespace Kunstmaan\MenuBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\MenuBundle\AdminList\MenuAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\MenuBundle\Entity\Menu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class MenuAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @param Request $request
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator(Request $request)
    {
        if (!isset($this->configurator)) {
            $this->configurator = new MenuAdminListConfigurator($this->getEntityManager());

            $create_route = function ($item) {
                return array(
                    'path'   => 'kunstmaanmenubundle_admin_menuitem',
                    'params' => array('menuid' => $item->getId())
                );
            };
            $this->configurator->addItemAction(new SimpleItemAction($create_route, 'th-list', 'Manage'));
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="kunstmaanmenubundle_admin_menu")
     *
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        // Make sure we have a menu for each possible locale
        $this->makeSureMenusExist();

        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

    /**
     * Make sure the menu objects exist in the database for each locale.
     */
    private function makeSureMenusExist()
    {
        $menuNames = $this->container->getParameter('kunstmaan_menu.menus');
        $locales = explode('|', $this->container->getParameter('requiredlocales'));
        $required = array();
        foreach ($menuNames as $name) {
            $required[$name] = $locales;
        }

        $em = $this->getDoctrine()->getManager();
        $menuObjects = $em->getRepository('KunstmaanMenuBundle:Menu')->findAll();
        foreach ($menuObjects as $menu) {
            if (array_key_exists($menu->getName(), $required)) {
                $index = array_search($menu->getLocale(), $required[$menu->getName()]);
                if ($index !== false) {
                    unset($required[$menu->getName()][$index]);
                }
            }
        }

        foreach ($required as $name => $locales) {
            foreach ($locales as $locale) {
                $menu = new Menu();
                $menu->setName($name);
                $menu->setLocale($locale);
                $em->persist($menu);
            }
        }

        $em->flush();
    }
}
