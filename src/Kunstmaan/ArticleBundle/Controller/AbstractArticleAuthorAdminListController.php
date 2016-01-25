<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\HttpFoundation\Request;

/**
 * The AdminList controller for the AbstractArticleAuthor
 */
abstract class AbstractArticleAuthorAdminListController extends AdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    protected $configurator;

    /**
     * @var EntityManager $em
     */
    protected $em;

    /**
     * @var string $locale
     */
    protected $locale;

    /**
     * @var User $user
     */
    protected $user;

    /**
     * @var AclHelper $aclHelper
     */
    protected $aclHelper;

    /**
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        $this->initAdminListConfigurator();
        if (!isset($this->configurator)) {
            $this->configurator = $this->createAdminListConfigurator();
        }

        return $this->configurator;
    }

    /**
     * @return AbstractArticlePageAdminListConfigurator
     */
    abstract  public function createAdminListConfigurator();

    protected function initAdminListConfigurator()
    {
        $this->em = $this->getEntityManager();
        $this->locale = $this->get('request_stack')->getCurrentRequest()->getLocale();
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
    }

    /**
     * The index action
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator(), $request);
    }

    /**
     * The add action
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator(), null, $request);
    }

    /**
     * The edit action
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * The delete action
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id, $request);
    }

    /**
     * Export action
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }
}
