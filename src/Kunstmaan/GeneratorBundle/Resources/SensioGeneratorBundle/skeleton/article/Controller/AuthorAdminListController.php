<?php

namespace {{ namespace }}\Controller;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\ArticleBundle\Controller\AbstractArticleAuthorAdminListController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use {{ namespace }}\AdminList\{{ entity_class }}AuthorAdminListConfigurator;
{% if isV4 %}

/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower}}-author", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class {{ entity_class }}AuthorAdminListController extends AbstractArticleAuthorAdminListController
{
    public function createAdminListConfigurator(): AdminListConfiguratorInterface
    {
        return new {{ entity_class }}AuthorAdminListConfigurator($this->getEntityManager(), $this->aclHelper, $this->locale, PermissionMap::PERMISSION_EDIT);
    }

    /**
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author")
     */
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

    /**
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator($request), null, $request);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, int $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_delete", methods={"GET", "POST"})
     */
    public function deleteAction(Request $request, int $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_export", methods={"GET", "POST"})
     */
    public function exportAction(Request $request, string $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator($request), $_format, $request);
    }
}
