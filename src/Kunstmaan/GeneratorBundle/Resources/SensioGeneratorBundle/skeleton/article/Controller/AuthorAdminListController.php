<?php

namespace {{ namespace }}\Controller;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\ArticleBundle\Controller\AbstractArticleAuthorAdminListController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use {{ namespace }}\AdminList\{{ entity_class }}AuthorAdminListConfigurator;
{% if isV4 %}

/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/author", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class {{ entity_class }}AuthorAdminListController extends AbstractArticleAuthorAdminListController
{
    /**
     * @return AdminListConfiguratorInterface
     */
    public function createAdminListConfigurator()
    {
        return new {{ entity_class }}AuthorAdminListConfigurator($this->getEntityManager(), $this->aclHelper, $this->locale, PermissionMap::PERMISSION_EDIT);
    }

    /**
     * The index action
     *
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

    /**
     * The add action
     *
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_add", methods={"GET", "POST"})
     * @return array
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator($request), null, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_edit", methods={"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_delete", methods={"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * Export action
     *
     * @param $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}author_export", methods={"GET", "POST"})
     *
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator($request), $_format, $request);
    }
}
