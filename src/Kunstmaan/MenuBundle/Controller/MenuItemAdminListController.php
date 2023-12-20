<?php

namespace Kunstmaan\MenuBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\MenuBundle\Entity\BaseMenu;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class MenuItemAdminListController extends AbstractAdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;
    /** @var DomainConfigurationInterface */
    private $domainConfiguration;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(DomainConfigurationInterface $domainConfiguration, EntityManagerInterface $em)
    {
        $this->domainConfiguration = $domainConfiguration;
        $this->em = $em;
    }

    /**
     * @param int $menuid
     * @param int $entityId
     *
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator(Request $request, $menuid, $entityId = null)
    {
        if (!isset($this->configurator)) {
            $menu = $this->em->getRepository($this->getParameter('kunstmaan_menu.entity.menu.class'))->find($menuid);
            $rootNode = $this->domainConfiguration->getRootNode();

            $configuratorClass = $this->getParameter('kunstmaan_menu.adminlist.menuitem_configurator.class');
            $this->configurator = new $configuratorClass($this->getEntityManager(), null, $menu);

            $adminType = $this->getParameter('kunstmaan_menu.form.menuitem_admintype.class');
            $menuItemClass = $this->getParameter('kunstmaan_menu.entity.menuitem.class');
            $this->configurator->setAdminType($adminType);
            $this->configurator->setAdminTypeOptions(['menu' => $menu, 'rootNode' => $rootNode, 'menuItemClass' => $menuItemClass, 'entityId' => $entityId, 'locale' => $request->getLocale()]);
        }

        return $this->configurator;
    }

    /**
     * @param int $menuid
     *
     * @return Response
     */
    #[Route(path: '/{menuid}/items', name: 'kunstmaanmenubundle_admin_menuitem')]
    public function indexAction(Request $request, $menuid)
    {
        $menuRepo = $this->em->getRepository($this->getParameter('kunstmaan_menu.entity.menu.class'));

        /** @var BaseMenu $menu */
        $menu = $menuRepo->find($menuid);
        if ($menu->getLocale() != $request->getLocale()) {
            /** @var BaseMenu $translatedMenu */
            $translatedMenu = $menuRepo->findOneBy(['locale' => $request->getLocale(), 'name' => $menu->getName()]);
            $menuid = $translatedMenu->getId();
        }

        $configurator = $this->getAdminListConfigurator($request, $menuid);
        $itemRoute = function (EntityInterface $item) use ($menuid) {
            return [
                'path' => 'kunstmaanmenubundle_admin_menuitem_move_up',
                'params' => [
                    'menuid' => $menuid,
                    'item' => $item->getId(),
                ],
            ];
        };
        $configurator->addItemAction(new SimpleItemAction($itemRoute, 'arrow-up', 'kuma_admin_list.action.move_up'));

        $itemRoute = function (EntityInterface $item) use ($menuid) {
            return [
                'path' => 'kunstmaanmenubundle_admin_menuitem_move_down',
                'params' => [
                    'menuid' => $menuid,
                    'item' => $item->getId(),
                ],
            ];
        };
        $configurator->addItemAction(new SimpleItemAction($itemRoute, 'arrow-down', 'kuma_admin_list.action.move_down'));

        return parent::doIndexAction($configurator, $request);
    }

    /**
     * @return Response
     */
    #[Route(path: '/{menuid}/items/add', name: 'kunstmaanmenubundle_admin_menuitem_add', methods: ['GET', 'POST'])]
    public function addAction(Request $request, $menuid)
    {
        return parent::doAddAction($this->getAdminListConfigurator($request, $menuid), null, $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '{menuid}/items/{id}/edit', requirements: ['id' => '\d+'], name: 'kunstmaanmenubundle_admin_menuitem_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, $menuid, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator($request, $menuid, $id), $id, $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '{menuid}/items/{id}/delete', requirements: ['id' => '\d+'], name: 'kunstmaanmenubundle_admin_menuitem_delete', methods: ['GET', 'POST'])]
    public function deleteAction(Request $request, $menuid, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request, $menuid), $id, $request);
    }

    /**
     * @return RedirectResponse
     */
    #[Route(path: '{menuid}/items/{item}/move-up', name: 'kunstmaanmenubundle_admin_menuitem_move_up', methods: ['GET'])]
    public function moveUpAction(Request $request, $menuid, $item)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository($this->getParameter('kunstmaan_menu.entity.menuitem.class'));
        $item = $repo->find($item);

        if ($item) {
            $repo->moveUp($item);
        }

        return new RedirectResponse(
            $this->generateUrl('kunstmaanmenubundle_admin_menuitem', ['menuid' => $menuid])
        );
    }

    /**
     * @return RedirectResponse
     */
    #[Route(path: '{menuid}/items/{item}/move-down', name: 'kunstmaanmenubundle_admin_menuitem_move_down', methods: ['GET'])]
    public function moveDownAction(Request $request, $menuid, $item)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository($this->getParameter('kunstmaan_menu.entity.menuitem.class'));
        $item = $repo->find($item);

        if ($item) {
            $repo->moveDown($item);
        }

        return new RedirectResponse(
            $this->generateUrl('kunstmaanmenubundle_admin_menuitem', ['menuid' => $menuid])
        );
    }
}
