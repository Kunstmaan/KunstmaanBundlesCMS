<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractArticleEntityAdminListController extends AbstractAdminListController
{
    /**
     * @var AdminListConfiguratorInterface
     */
    protected $configurator;

    /**
     * @var EntityManagerInterface
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
     * @return AdminListConfiguratorInterface
     */
    protected function getAdminListConfigurator(Request $request)
    {
        $this->initAdminListConfigurator($request);
        if (!isset($this->configurator)) {
            $this->configurator = $this->createAdminListConfigurator();
        }

        return $this->configurator;
    }

    /**
     * @return AbstractArticlePageAdminListConfigurator|AdminListConfiguratorInterface
     */
    abstract protected function createAdminListConfigurator();

    protected function initAdminListConfigurator(Request $request)
    {
        $this->em = $this->getEntityManager();
        $this->locale = $request->getLocale();
        $this->user = $this->container->get('security.token_storage')->getToken()->getUser();
        $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
    }

    public static function getSubscribedServices(): array
    {
        return [
            'kunstmaan_admin.acl.helper' => AclHelper::class,
        ] + parent::getSubscribedServices();
    }
}
