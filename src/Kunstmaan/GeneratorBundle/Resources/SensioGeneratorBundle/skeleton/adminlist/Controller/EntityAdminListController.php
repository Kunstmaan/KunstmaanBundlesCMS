<?php

namespace {{ namespace }}\Controller;

use {{ namespace }}\AdminList\{{ entity_class }}AdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

{% if canUseAttributes %}
#[Route('/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower }}', requirements: ['_locale' => '%requiredlocales%'])]
{% else %}
/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower }}", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class {{ entity_class }}AdminListController extends AbstractAdminListController
{
    /** @var {{ entity_class }}AdminListConfigurator */
    private $configurator;

    public function getAdminListConfigurator(): {{ entity_class }}AdminListConfigurator
    {
        if (!isset($this->configurator)) {
            $this->configurator = new {{ entity_class }}AdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

{% if canUseAttributes %}
    #[Route('/', name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}')]
{% else %}
    /**
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}")
     */
{% endif %}
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

{% if canUseAttributes %}
    #[Route('/add', name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_add', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_add", methods={"GET", "POST"})
     */
{% endif %}
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_edit', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_edit", methods={"GET", "POST"})
     */
{% endif %}
    public function editAction(Request $request, int $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/view', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_view', methods: ['GET'])]
{% else %}
    /**
     * @Route("/{id}/view", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_view", methods={"GET"})
     */
{% endif %}
    public function viewAction(Request $request, int $id): Response
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/delete', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_delete', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_delete", methods={"GET", "POST"})
     */
{% endif %}
    public function deleteAction(Request $request, int $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/export.{_format}', requirements: ['_format' => '{{ export_extensions }}'], name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_export', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/export.{_format}", requirements={"_format" = "{{ export_extensions }}"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_export", methods={"GET", "POST"})
     */
{% endif %}
    public function exportAction(Request $request, string $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
{% if sortField %}

{% if canUseAttributes %}
    #[Route('/{id}/move-up', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_move_up', methods: ['GET'])]
{% else %}
    /**
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_move_up", methods={"GET"})
     */
{% endif %}
    public function moveUpAction(Request $request, int $id): Response
    {
    return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/move-down', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_move_down', methods: ['GET'])]
{% else %}
    /**
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_move_down", methods={"GET"})
     */
{% endif %}
    public function moveDownAction(Request $request, int $id): Response
    {
    return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }
{% endif %}
}
