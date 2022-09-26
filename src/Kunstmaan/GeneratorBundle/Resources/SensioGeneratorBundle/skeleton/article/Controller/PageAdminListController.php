<?php

namespace {{ namespace }}\Controller;

use {{ namespace }}\AdminList\{{ entity_class }}PageAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\ArticleBundle\Controller\AbstractArticlePageAdminListController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

{% if canUseAttributes %}
#[Route('/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower}}-page', requirements: ['_locale' => '%requiredlocales%'])]
{% else %}
/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower}}-page", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class {{ entity_class }}PageAdminListController extends AbstractArticlePageAdminListController
{
    public function createAdminListConfigurator(): AdminListConfiguratorInterface
    {
        return new {{ entity_class }}PageAdminListConfigurator($this->getEntityManager(), $this->aclHelper, $this->locale, PermissionMap::PERMISSION_EDIT);
    }

{% if canUseAttributes %}
    #[Route('/', name: '{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page')]
{% else %}
    /**
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page")
     */
{% endif %}
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

{% if canUseAttributes %}
    #[Route('/add', name: '{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_add', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_add", methods={"GET", "POST"})
     */
{% endif %}
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator($request), null, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_edit', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_edit", methods={"GET", "POST"})
     */
{% endif %}
    public function editAction(Request $request, int $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator($request), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/{id}/delete', requirements: ['id' => '\d+'], name: '{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_delete', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_delete", methods={"GET", "POST"})
     */
{% endif %}
    public function deleteAction(Request $request, int $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request), $id, $request);
    }

{% if canUseAttributes %}
    #[Route('/export.{_format}', requirements: ['_format' => 'csv'], name: '{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_export', methods: ['GET', 'POST'])]
{% else %}
    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="{{ bundle.getName()|lower }}_admin_pages_{{ entity_class|lower }}page_export", methods={"GET", "POST"})
     */
{% endif %}
    public function exportAction(Request $request, string $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator($request), $_format, $request);
    }
}
