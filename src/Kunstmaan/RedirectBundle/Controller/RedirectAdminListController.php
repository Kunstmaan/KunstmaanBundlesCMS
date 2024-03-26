<?php

namespace Kunstmaan\RedirectBundle\Controller;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RedirectAdminListController extends AbstractAdminListController
{
    /**
     * @var DomainConfigurationInterface|AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @param DomainConfigurationInterface|AdminListConfiguratorInterface $configurator
     */
    public function __construct(/**AdminListConfiguratorInterface*/ $configurator)
    {
        $this->configurator = $configurator;
        if (!$configurator instanceof AdminListConfiguratorInterface) {
            trigger_deprecation('kunstmaan/redirect-bundle', '6.4', 'Passing a "DomainConfigurationInterface" instance for the first parameter in "%s" is deprecated and a AdminListConfiguratorInterface instance will be required in 7.0.', __METHOD__);
        }
    }

    public function getAdminListConfigurator(): AdminListConfiguratorInterface
    {
        if ($this->configurator instanceof AdminListConfiguratorInterface) {
            return $this->configurator;
        }

        return new RedirectAdminListConfigurator($this->getEntityManager(), null, $this->configurator);
    }

    #[Route(path: '/', name: 'kunstmaanredirectbundle_admin_redirect')]
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    #[Route(path: '/add', name: 'kunstmaanredirectbundle_admin_redirect_add', methods: ['GET', 'POST'])]
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], name: 'kunstmaanredirectbundle_admin_redirect_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     */
    #[Route(path: '/{id}/delete', requirements: ['id' => '\d+'], name: 'kunstmaanredirectbundle_admin_redirect_delete', methods: ['GET', 'POST'])]
    public function deleteAction(Request $request, $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param string $_format
     */
    #[Route(path: '/export.{_format}', requirements: ['_format' => 'csv|xlsx|ods'], name: 'kunstmaanredirectbundle_admin_redirect_export', methods: ['GET', 'POST'])]
    public function exportAction(Request $request, $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
}
