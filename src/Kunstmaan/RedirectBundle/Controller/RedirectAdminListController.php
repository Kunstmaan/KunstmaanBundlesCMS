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
        if (!isset($this->configurator)) {
            $this->configurator = new RedirectAdminListConfigurator($this->getEntityManager(), null, $this->domainConfiguration);
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="kunstmaanredirectbundle_admin_redirect")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The add action
     *
     * @Route("/add", name="kunstmaanredirectbundle_admin_redirect_add", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="kunstmaanredirectbundle_admin_redirect_edit", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaanredirectbundle_admin_redirect_delete", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx|ods"}, name="kunstmaanredirectbundle_admin_redirect_export", methods={"GET", "POST"})
     *
     * @return Response
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
}
