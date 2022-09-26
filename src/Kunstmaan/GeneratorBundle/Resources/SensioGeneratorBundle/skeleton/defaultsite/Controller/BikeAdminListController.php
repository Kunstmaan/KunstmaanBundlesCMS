<?php

namespace {{ namespace }}\Controller;

use {{ namespace }}\AdminList\BikeAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

{% if canUseAttributes %}#[Route('/{_locale}/%kunstmaan_admin.admin_prefix%/bike', requirements: ['_locale' => '%requiredlocales%'])]
{% else %}/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/bike", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class BikeAdminListController extends AbstractAdminListController
{
    /** @var BikeAdminListConfigurator|null */
    private $configurator;

    public function getAdminListConfigurator(): BikeAdminListConfigurator
    {
        if (!isset($this->configurator)) {
            $this->configurator = new BikeAdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

{% if canUseAttributes %}
    #[Route('/', name: '{{ bundle_name|lower }}_admin_bike')]
{% else %}
    /**
     * @Route("/", name="{{ bundle_name|lower }}_admin_bike")
     */
{% endif %}
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

{% if canUseAttributes %}
    #[Route('/add', name: '{{ bundle_name|lower }}_admin_bike_add', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/add", name="{{ bundle_name|lower }}_admin_bike_add", methods={"GET", "POST"})
     */
{% endif %}
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}', requirements: ['id' => '\d+'], name: '{{ bundle_name|lower }}_admin_bike_edit', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_edit", methods={"GET", "POST"})
     */
{% endif %}
    public function editAction(Request $request, int $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/delete', requirements: ['id' => '\d+'], name: '{{ bundle_name|lower }}_admin_bike_delete', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_delete", methods={"GET", "POST"})
     */
{% endif %}
    public function deleteAction(Request $request, int $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/export.{_format}', requirements: ['_format' => 'csv'], name: '{{ bundle_name|lower }}_admin_bike_export', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="{{ bundle_name|lower }}_admin_bike_export", methods={"GET", "POST"})
     */
{% endif %}
    public function exportAction(Request $request, string $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/move-up', requirements: ['id' => '\d+'], name: '{{ bundle_name|lower }}_admin_bike_move_up', methods: ['GET'])]
{% else %}
    /**
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_move_up", methods={"GET"})
     */
{% endif %}
    public function moveUpAction(Request $request, int $id): RedirectResponse
    {
        return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/move-down', requirements: ['id' => '\d+'], name: '{{ bundle_name|lower }}_admin_bike_move_down', methods: ['GET'])]
{% else %}
    /**
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_move_down", methods={"GET"})
     */
{% endif %}
    public function moveDownAction(Request $request, int $id): RedirectResponse
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }
}
