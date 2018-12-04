<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractArticleEntityAdminListController
 */
abstract class AbstractArticleEntityAdminListController extends AdminListController
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
     * @var BaseUser
     */
    protected $user;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * NEXT_MAJOR: change method visibility from public to protected
     *
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
     * NEXT_MAJOR: change method visibility from public to protected
     *
     * @return AbstractArticlePageAdminListConfigurator
     */
    abstract public function createAdminListConfigurator();

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
}
