<?php

namespace {{ namespace }}\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use {{ namespace }}\AdminList\BikeAdminListConfigurator;

{% if isV4 %}

/**
 * @Route("/{_locale}/%kunstmaan_admin.admin_prefix%/bike", requirements={"_locale"="%requiredlocales%"})
 */
{% endif %}
class BikeAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
	if (!isset($this->configurator)) {
	    $this->configurator = new BikeAdminListConfigurator($this->getEntityManager());
	}

	return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="{{ bundle_name|lower }}_admin_bike")
     */
    public function indexAction(Request $request)
    {
	return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The add action
     *
     * @Route("/add", name="{{ bundle_name|lower }}_admin_bike_add", methods={"GET", "POST"})
     * @return array
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
     * @Route("/{id}", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_edit", methods={"GET", "POST"})
     *
     * @return array
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
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_delete", methods={"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
	return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @Route("/export.{_format}", requirements={"_format" = "csv"}, name="{{ bundle_name|lower }}_admin_bike_export", methods={"GET", "POST"})
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
	return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * The move up action
     *
     * @param int $id
     *
     * @Route("/{id}/move-up", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_move_up", methods={"GET"})
     *
     * @return array
     */
    public function moveUpAction(Request $request, $id)
    {
        return parent::doMoveUpAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The move down action
     *
     * @param int $id
     *
     * @Route("/{id}/move-down", requirements={"id" = "\d+"}, name="{{ bundle_name|lower }}_admin_bike_move_down", methods={"GET"})
     *
     * @return array
     */
    public function moveDownAction(Request $request, $id)
    {
        return parent::doMoveDownAction($this->getAdminListConfigurator(), $id, $request);
    }
}
