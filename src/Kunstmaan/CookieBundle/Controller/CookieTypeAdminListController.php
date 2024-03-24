<?php

namespace Kunstmaan\CookieBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\CookieBundle\AdminList\CookieTypeAdminListConfigurator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CookieTypeAdminListController
 */
final class CookieTypeAdminListController extends AbstractAdminListController
{
    /* @var AdminListConfiguratorInterface */
    private $configurator;

    public function getAdminListConfigurator(): AdminListConfiguratorInterface
    {
        if (null === $this->configurator) {
            $this->configurator = new CookieTypeAdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

    #[Route(path: '/', name: 'kunstmaancookiebundle_admin_cookietype')]
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    #[Route(path: '/add', name: 'kunstmaancookiebundle_admin_cookietype_add', methods: ['GET', 'POST'])]
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookietype_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}/view', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookietype_view', methods: ['GET'])]
    public function viewAction(Request $request, $id): Response
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}/delete', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookietype_delete', methods: ['GET'])]
    public function deleteAction(Request $request, $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param string $_format
     */
    #[Route(path: '/export.{_format}', requirements: ['_format' => 'csv|ods|xlsx'], name: 'kunstmaancookiebundle_admin_cookietype_export', methods: ['GET', 'POST'])]
    public function exportAction(Request $request, $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}/move-up', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookietype_move_up', methods: ['GET'])]
    public function moveUpAction(Request $request, $id): RedirectResponse
    {
        return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}/move-down', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookietype_move_down', methods: ['GET'])]
    public function moveDownAction(Request $request, $id): RedirectResponse
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }
}
