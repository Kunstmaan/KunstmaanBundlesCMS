<?php

namespace Kunstmaan\MenuBundle\Controller;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\MenuBundle\Service\MenuService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MenuAdminListController extends AbstractAdminListController
{
    /** @var AdminListConfiguratorInterface */
    private $configurator;
    /** @var MenuService */
    private $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator(Request $request)
    {
        if (!isset($this->configurator)) {
            $configuratorClass = $this->getParameter('kunstmaan_menu.adminlist.menu_configurator.class');
            $this->configurator = new $configuratorClass(
                $this->getEntityManager()
            );

            $create_route = function (EntityInterface $item) {
                return [
                    'path' => 'kunstmaanmenubundle_admin_menuitem',
                    'params' => ['menuid' => $item->getId()],
                ];
            };
            $this->configurator->addItemAction(
                new SimpleItemAction($create_route, 'th-list', 'kuma_menu.menu.adminlist.action.manage')
            );
            $this->configurator->setLocale($request->getLocale());
        }

        return $this->configurator;
    }

    /**
     * @return Response
     */
    #[Route(path: '/', name: 'kunstmaanmenubundle_admin_menu')]
    public function indexAction(Request $request)
    {
        // Make sure we have a menu for each possible locale
        $this->menuService->makeSureMenusExist();

        return parent::doIndexAction(
            $this->getAdminListConfigurator($request),
            $request
        );
    }
}
