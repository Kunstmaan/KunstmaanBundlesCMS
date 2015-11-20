<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\LeadGenerationBundle\AdminList\PopupAdminListConfigurator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PopupsAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    private $configurator;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator($listAction = false)
    {
        if (!isset($this->configurator)) {
            $this->configurator = new PopupAdminListConfigurator($this->getEntityManager());

            if ($listAction) {
                $create_route = function (EntityInterface $item) {
                    return array(
                        'path' => 'kunstmaanleadgenerationbundle_admin_rule_abstractrule_detail',
                        'params' => array('popup' => $item->getId())
                    );
                };
                $this->configurator->addItemAction(new SimpleItemAction($create_route, 'th-list', 'Manage rules'));
            }
        }

        return $this->configurator;
    }

    /**
     * The index action
     *
     * @Route("/", name="kunstmaanleadgenerationbundle_admin_popup_abstractpopup")
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(true), $request);
    }

    /**
     * The delete action
     *
     * @param int $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="kunstmaanleadgenerationbundle_admin_popup_abstractpopup_delete")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The edit action
     *
     * @param int $id
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="kunstmaanleadgenerationbundle_admin_popup_abstractpopup_edit")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The add action
     *
     * @Route("/add",  name="kunstmaanleadgenerationbundle_admin_popup_abstractpopup_add")
     * @Method({"GET", "POST"})
     *
     * @return array
     */
    public function addAction(Request $request)
    {
        $type = $request->get('type');

        return parent::doAddAction($this->getAdminListConfigurator(), $type, $request);
    }
}
