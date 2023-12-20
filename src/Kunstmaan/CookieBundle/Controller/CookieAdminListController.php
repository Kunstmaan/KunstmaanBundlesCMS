<?php

namespace Kunstmaan\CookieBundle\Controller;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\CookieBundle\AdminList\CookieAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CookieAdminListController extends AbstractAdminListController
{
    /** @var AdminListConfiguratorInterface */
    private $configurator;

    /** @var DomainConfigurationInterface */
    private $domainConfiguration;

    public function __construct(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (null === $this->configurator) {
            $this->configurator = new CookieAdminListConfigurator($this->getEntityManager(), null, $this->domainConfiguration);
        }

        return $this->configurator;
    }

    /**
     * @return Response
     */
    #[Route(path: '/', name: 'kunstmaancookiebundle_admin_cookie')]
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @return Response
     */
    #[Route(path: '/add', name: 'kunstmaancookiebundle_admin_cookie_add', methods: ['GET', 'POST'])]
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookie_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '/{id}/view', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookie_view', methods: ['GET'])]
    public function viewAction(Request $request, $id)
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '/{id}/delete', requirements: ['id' => '\d+'], name: 'kunstmaancookiebundle_admin_cookie_delete', methods: ['GET', 'POST'])]
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param string $_format
     *
     * @return Response
     */
    #[Route(path: '/export.{_format}', requirements: ['_format' => 'csv|ods|xlsx'], name: 'kunstmaancookiebundle_admin_cookie_export', methods: ['GET', 'POST'])]
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
}
