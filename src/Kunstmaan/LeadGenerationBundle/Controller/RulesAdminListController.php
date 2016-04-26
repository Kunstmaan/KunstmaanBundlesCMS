<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\LeadGenerationBundle\AdminList\RulesAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class RulesAdminListController extends AdminListController
{
    /**
     * @var RulesAdminListConfigurator
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator($id)
    {
        if (!isset($this->configurator)) {
            $this->configurator = new RulesAdminListConfigurator($this->getEntityManager(), null, $id);
        }

        return $this->configurator;
    }

    /**
     * The detail action
     *
     * @Route("/{popup}/rules", requirements={"popup" = "\d+"}, name="kunstmaanleadgenerationbundle_admin_rule_abstractrule_detail")
     */
    public function detailAction(Request $request, $popup)
    {
        return parent::doIndexAction($this->getAdminListConfigurator($popup), $request);
    }

    /**
     * The add action
     *
     * @Route("/{popup}/add", requirements={"popup" = "\d+"}, name="kunstmaanleadgenerationbundle_admin_rule_abstractrule_add")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function addAction(Request $request, $popup)
    {
        $type = $request->get('type');

        return parent::doAddAction($this->getAdminListConfigurator($popup), $type, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{popup}/rules/{id}/edit", requirements={"popup" = "\d+", "rule" = "\d+"}, name="kunstmaanleadgenerationbundle_admin_rule_abstractrule_edit")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $id, $popup)
    {
        return parent::doEditAction($this->getAdminListConfigurator($popup), $id, $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{popup}/rules/{id}/delete", requirements={"popup" = "\d+"}, name="kunstmaanleadgenerationbundle_admin_rule_abstractrule_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id, $popup)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($popup), $id, $request);
    }
}
