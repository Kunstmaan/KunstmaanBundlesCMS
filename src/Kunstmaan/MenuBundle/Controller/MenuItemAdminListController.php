<?php

namespace Kunstmaan\MenuBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\MenuBundle\AdminList\MenuItemAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\MenuBundle\Form\MenuItemAdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MenuItemAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @param Request $request
     * @param int $menuid
     * @param int $entityId
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator(Request $request, $menuid, $entityId = null)
    {
        if (!isset($this->configurator)) {
            $menu = $this->getDoctrine()->getManager()->getRepository('KunstmaanMenuBundle:Menu')->find($menuid);
            $this->configurator = new MenuItemAdminListConfigurator($this->getEntityManager(), null, $menu);
            $this->configurator->setAdminType(new MenuItemAdminType($request->getLocale(), $menu, $entityId));
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/{menuid}/items", name="kunstmaanmenubundle_admin_menuitem")
     */
    public function indexAction(Request $request, $menuid)
    {
        $result = $this->checkMenuLocale($request, $menuid, 'kunstmaanmenubundle_admin_menuitem');
        if ($result) {
            return $result;
        }

        return parent::doIndexAction($this->getAdminListConfigurator($request, $menuid), $request);
    }

    /**
     * The add action
     *
     * @Route("/{menuid}/items/add", name="kunstmaanmenubundle_admin_menuitem_add")
     * @Method({"GET", "POST"})
     * @return array
     */
    public function addAction(Request $request, $menuid)
    {
        $result = $this->checkMenuLocale($request, $menuid, 'kunstmaanmenubundle_admin_menuitem_add');
        if ($result) {
            return $result;
        }

        return parent::doAddAction($this->getAdminListConfigurator($request, $menuid), null, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("{menuid}/items/{id}/edit", requirements={"id" = "\d+"}, name="kunstmaanmenubundle_admin_menuitem_edit")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $menuid, $id)
    {
        $result = $this->checkMenuLocale($request, $menuid, 'kunstmaanmenubundle_admin_menuitem_edit', $id);
        if ($result) {
            return $result;
        }

        return parent::doEditAction($this->getAdminListConfigurator($request, $menuid, $id), $id, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("{menuid}/items/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaanmenubundle_admin_menuitem_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $menuid, $id)
    {
        $result = $this->checkMenuLocale($request, $menuid, 'kunstmaanmenubundle_admin_menuitem_delete', $id);
        if ($result) {
            return $result;
        }

        return parent::doDeleteAction($this->getAdminListConfigurator($request, $menuid), $id, $request);
    }

    /**
     * @param Request $request
     * @param int $menuid
     * @return RedirectResponse|null
     */
    private function checkMenuLocale($request, $menuid, $routeName, $id = null)
    {
        // We need to make sure the menu locale matches the admin locale
        $menu = $this->getEntityManager()->getRepository('KunstmaanMenuBundle:Menu')->find($menuid);
        if ($menu && $menu->getLocale() != $request->getLocale()) {
            $url = $this->generateUrl($routeName, array(
                'menuid' => $menuid,
                '_locale' => $menu->getLocale(),
                'id' => $id
            ));

            return new RedirectResponse($url);
        }

        return null;
    }
}
