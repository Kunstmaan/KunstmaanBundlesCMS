<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractArticleAdminListController
 * @package Kunstmaan\ArticleBundle\Controller
 */
abstract class AbstractArticleAdminListController extends AdminListController
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
    public function getAdminListConfigurator(Request $request)
    {
        $this->initAdminListConfigurator($request);
        if (!isset($this->configurator)) {
            $this->configurator = $this->createAdminListConfigurator();
        }

        return $this->configurator;
    }

    /**
     * @param Request $request
     */
    protected function initAdminListConfigurator(Request $request)
    {
        $this->em = $this->getEntityManager();
        $this->locale = $request->getLocale();
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return parent::doIndexAction($this->getAdminListConfigurator($request), $request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        return parent::doAddAction($this->getAdminListConfigurator($request), null, $request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        return parent::doEditAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator($request), $id, $request);
    }

    /**
     * @param Request $request
     * @param $_format
     * @return \Symfony\Component\HttpFoundation\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator($request), $_format, $request);
    }

    /**
     * @return mixed
     */
    abstract public function createAdminListConfigurator();
}

