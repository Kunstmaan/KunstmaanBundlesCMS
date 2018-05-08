<?php

namespace Kunstmaan\MenuBundle\Controller;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class MenuAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @param Request $request
     *
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator(Request $request)
    {
        if (!isset($this->configurator)) {
            $configuratorClass = $this->container->getParameter('kunstmaan_menu.adminlist.menu_configurator.class');
            $this->configurator = new $configuratorClass(
                $this->getEntityManager()
            );

            $create_route = function (EntityInterface $item) {
                return array(
                    'path' => 'kunstmaanmenubundle_admin_menuitem',
                    'params' => array('menuid' => $item->getId()),
                );
            };
            $this->configurator->addItemAction(
                new SimpleItemAction($create_route, 'th-list', 'kuma_menu.menu.adminlist.action.manage')
            );
            $this->configurator->setLocale($request->getLocale());
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="kunstmaanmenubundle_admin_menu")
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        // Make sure we have a menu for each possible locale
        $this->container->get('kunstmaan_menu.menu.service')->makeSureMenusExist();

        return parent::doIndexAction(
            $this->getAdminListConfigurator($request),
            $request
        );
    }
}
