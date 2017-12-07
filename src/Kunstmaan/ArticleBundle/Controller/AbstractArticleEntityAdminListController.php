<?php

namespace Kunstmaan\ArticleBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Entity\BaseUser;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Traits\DependencyInjection\AclHelperTrait;
use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;
use Symfony\Component\CssSelector\XPath\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AbstractArticleEntityAdminListController
 */
abstract class AbstractArticleEntityAdminListController extends AdminListController
{
    use AclHelperTrait;

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
     * @var BaseUser $user
     */
    protected $user;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * AbstractArticleEntityAdminListController constructor.
     *
     * @param EventDispatcherInterface|null $eventDispatcher
     * @param EntityManagerInterface|null   $entityManager
     * @param AclHelper|null                $aclHelper
     * @param int                           $entityVersionLockInterval
     * @param bool                          $entityVersionLockCheck
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher = null,
        EntityManagerInterface $entityManager = null,
        AdminListFactory $adminListFactory = null,
        AclHelper $aclHelper = null,
        TranslatorInterface $translator = null,
        $entityVersionLockInterval = 15,
        $entityVersionLockCheck = false)
    {
        parent::__construct($eventDispatcher, $entityManager, $adminListFactory, $translator, $entityVersionLockInterval, $entityVersionLockCheck);

        $this->setAclHelper($aclHelper);
    }

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

    /**
     * @param Request $request
     */
    protected function initAdminListConfigurator(Request $request)
    {
        $this->em = $this->getEntityManager();
        $this->locale = $request->getLocale();
        $this->user = $this->getUser();

        if (null !== $this->container && null === $this->aclHelper) {
            $this->aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
        }
    }
}
