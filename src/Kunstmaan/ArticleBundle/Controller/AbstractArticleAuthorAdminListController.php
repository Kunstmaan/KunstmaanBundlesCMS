<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\HttpFoundation\Request;

/**
 * The AdminList controller for the AbstractArticleAuthor.
 */
abstract class AbstractArticleAuthorAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    protected $configurator;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator(Request $request)
    {
        $this->initAdminListConfigurator($request);
        if (!isset($this->configurator)) {
            $this->configurator = $this->createAdminListConfigurator();
        }

        return $this->configurator;
    }

    /**
     * @return AbstractArticlePageAdminListConfigurator
     */
    abstract public function createAdminListConfigurator();

    protected function initAdminListConfigurator(Request $request)
    {
        $this->em = $this->getEntityManager();
        $this->locale = $request->getLocale();
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
    }

    /**
     * The index action.
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

    /**
     * The add action.
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator($request), null, $request);
    }

    /**
     * The edit action.
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * The delete action.
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * Export action.
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator($request), $_format, $request);
    }
}
