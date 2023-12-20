<?php

namespace Kunstmaan\LeadGenerationBundle\Controller;

use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\LeadGenerationBundle\AdminList\PopupAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PopupsAdminListController extends AbstractAdminListController
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
                    return [
                        'path' => 'kunstmaanleadgenerationbundle_admin_rule_abstractrule_detail',
                        'params' => ['popup' => $item->getId()],
                    ];
                };
                $this->configurator->addItemAction(new SimpleItemAction($create_route, 'th-list', 'Manage rules'));
            }
        }

        return $this->configurator;
    }

    /**
     * @return Response
     */
    #[Route(path: '/', name: 'kunstmaanleadgenerationbundle_admin_popup_abstractpopup')]
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(true), $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '/{id}/delete', requirements: ['id' => '\d+'], name: 'kunstmaanleadgenerationbundle_admin_popup_abstractpopup_delete', methods: ['GET', 'POST'])]
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @param int $id
     *
     * @return Response
     */
    #[Route(path: '/{id}/edit', requirements: ['id' => '\d+'], name: 'kunstmaanleadgenerationbundle_admin_popup_abstractpopup_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * @return Response
     */
    #[Route(path: '/add', name: 'kunstmaanleadgenerationbundle_admin_popup_abstractpopup_add', methods: ['GET', 'POST'])]
    public function addAction(Request $request)
    {
        $type = $request->query->get('type');

        return parent::doAddAction($this->getAdminListConfigurator(), $type, $request);
    }
}
