<?php

namespace {{ namespace }}\Controller;

use {{ namespace }}\AdminList\{{ entity_class }}AdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

{% if isV4 %}

/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower }}", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class {{ entity_class }}AdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    public function getAdminListConfigurator(): AdminListConfiguratorInterface
    {
        if (!isset($this->configurator)) {
            $this->configurator = new {{ entity_class }}AdminListConfigurator($this->getEntityManager());
        }

        return $this->configurator;
    }

    /**
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}")
     */
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, int $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/view", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_view", methods={"GET"})
     */
    public function viewAction(Request $request, int $id): Response
    {
        return parent::doViewAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_delete", methods={"GET", "POST"})
     */
    public function deleteAction(Request $request, int $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/export.{_format}", requirements={"_format" = "{{ export_extensions }}"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_export", methods={"GET", "POST"})
     */
    public function exportAction(Request $request, string $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
{% if sortField %}

    /**
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_move_up", methods={"GET"})
     */
    public function moveUpAction(Request $request, int $id): Response
    {
    return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}_move_down", methods={"GET"})
     */
    public function moveDownAction(Request $request, int $id): Response
    {
    return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }
{% endif %}
}
