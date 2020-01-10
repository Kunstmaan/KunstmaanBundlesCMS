<?php

namespace {{ namespace }}\Controller;

use {{ namespace }}\AdminList\{{ entity_class }}TagAdminListConfigurator;
use Kunstmaan\ArticleBundle\Controller\AbstractArticleTagAdminListController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
{% if isV4 %}

/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/{{ entity_class|lower}}-tag", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class {{ entity_class }}TagAdminListController extends AbstractArticleTagAdminListController
{
    /**
     * @Route("/", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag")
     */
    public function indexAction(Request $request): Response
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

    /**
     * @Route("/add", name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request): Response
    {
        return parent::doAddAction($this->getAdminListConfigurator($request), null, $request);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, int $id): Response
    {
        return parent::doEditAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_view", methods={"GET"})
     */
    public function viewAction(Request $request, int $id): Response
    {
        return parent::doViewAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_delete", methods={"GET", "POST"})
     */
    public function deleteAction(Request $request, int $id): Response
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv|xlsx|ods"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_export", methods={"GET", "POST"})
     */
    public function exportAction(Request $request, string $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator($request), $_format, $request);
    }

    /**
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_move_up", methods={"GET"})
     */
    public function moveUpAction(Request $request, int $id): Response
    {
        return parent::doMoveUpAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="{{ bundle.getName()|lower }}_admin_{{ entity_class|lower }}tag_move_down", methods={"GET"})
     */
    public function moveDownAction(Request $request, int $id): Response
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator($request), $id, $request);
    }

    public function createAdminListConfigurator(): {{ entity_class }}TagAdminListConfigurator
    {
        return new {{ entity_class }}TagAdminListConfigurator($this->em, $this->aclHelper);
    }
}
