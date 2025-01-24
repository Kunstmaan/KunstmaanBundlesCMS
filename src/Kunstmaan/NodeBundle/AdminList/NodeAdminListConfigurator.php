<?php

namespace Kunstmaan\NodeBundle\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\BooleanFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\ListAction\SimpleListAction;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * NodeAdminListConfigurator
 */
class NodeAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var string
     */
    protected $locale;

    /**
     * @var string
     */
    protected $permission;

    /**
     * @var DomainConfigurationInterface
     */
    protected $domainConfiguration;

    /**
     * @var bool
     */
    protected $showAddHomepage;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    private ?Node $node = null;

    /**
     * @param EntityManagerInterface $em         The entity manager
     * @param AclHelper              $aclHelper  The ACL helper
     * @param string                 $locale     The current locale
     * @param string                 $permission The permission
     */
    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper, $locale, $permission, AuthorizationCheckerInterface $authorizationChecker, ?Node $node = null)
    {
        parent::__construct($em, $aclHelper);
        $this->locale = $locale;
        $this->authorizationChecker = $authorizationChecker;
        $this->setPermissionDefinition(
            new PermissionDefinition(
                [$permission],
                'Kunstmaan\NodeBundle\Entity\Node',
                'n'
            )
        );
        $this->node = $node;
    }

    public function setDomainConfiguration(DomainConfigurationInterface $domainConfiguration)
    {
        $this->domainConfiguration = $domainConfiguration;
    }

    /**
     * @param bool $showAddHomepage
     */
    public function setShowAddHomepage($showAddHomepage)
    {
        $this->showAddHomepage = $showAddHomepage;
    }

    /**
     * Build list actions ...
     */
    public function buildListActions()
    {
        if (!$this->showAddHomepage) {
            return;
        }

        $addHomepageRoute = [
            'path' => '',
            'attributes' => [
                'class' => 'btn btn-default btn--raise-on-hover',
                'data-target' => '#add-homepage-modal',
                'data-keyboard' => 'true',
                'data-toggle' => 'modal',
                'type' => 'button',
            ],
        ];

        $this->addListAction(
            new SimpleListAction(
                $addHomepageRoute,
                'kuma_node.modal.add_homepage.h',
                null,
                '@KunstmaanNode/Admin/list_action_button.html.twig'
            )
        );
    }

    public function buildItemActions(): void
    {
        $locale = $this->locale;
        $acl = $this->authorizationChecker;

        $itemRoute = function (EntityInterface $item) use ($locale, $acl) {
            if ($acl->isGranted(PermissionMap::PERMISSION_VIEW, $item->getNode())) {
                return [
                    'path' => '_slug_preview',
                    'params' => ['_locale' => $locale, 'url' => $item->getUrl()],
                ];
            }
        };
        $this->addSimpleItemAction('action.preview', $itemRoute, 'eye');

        $nodeItemsRoute = function (EntityInterface $item) use ($acl) {
            $node = $item->getNode();
            if (!$acl->isGranted(PermissionMap::PERMISSION_VIEW, $node)) {
                return null;
            }

            if ($node->getChildren()->count() === 0) {
                return null;
            }

            return [
                'path' => 'KunstmaanNodeBundle_nodes_items',
                'params' => ['nodeId' => $node->getId()],
            ];
        };
        $this->addSimpleItemAction('action.list_nodes', $nodeItemsRoute, 'th-list');
    }

    /**
     * Configure filters
     */
    public function buildFilters()
    {
        $this
            ->addFilter('title', new StringFilterType('title'), 'kuma_node.admin.list.filter.title')
            ->addFilter('created', new DateFilterType('created'), 'kuma_node.admin.list.filter.created_at')
            ->addFilter('updated', new DateFilterType('updated'), 'kuma_node.admin.list.filter.updated_at')
            ->addFilter('online', new BooleanFilterType('online'), 'kuma_node.admin.list.filter.online');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this
            ->addField('title', 'kuma_node.admin.list.header.title', true, '@KunstmaanNode/Admin/title.html.twig')
            ->addField('created', 'kuma_node.admin.list.header.created_at', true)
            ->addField('updated', 'kuma_node.admin.list.header.updated_at', true)
            ->addField('online', 'kuma_node.admin.list.header.online', true, '@KunstmaanNode/Admin/online.html.twig');
    }

    /**
     * @return array
     */
    public function getEditUrlFor($item)
    {
        /* @var Node $node */
        $node = $item->getNode();

        return [
            'path' => 'KunstmaanNodeBundle_nodes_edit',
            'params' => ['id' => $node->getId()],
        ];
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    public function canEdit($item)
    {
        return $this->authorizationChecker->isGranted(PermissionMap::PERMISSION_EDIT, $item->getNode());
    }

    /**
     * Return if current user can delete the specified item
     *
     * @param array|object $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return [];
    }

    /**
     * @deprecated since 6.4. Use the `getEntityClass` method instead.
     *
     * @return string
     */
    public function getBundleName()
    {
        trigger_deprecation('kunstmaan/node-bundle', '6.4', 'The "%s" method is deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.', __METHOD__);

        return 'KunstmaanNodeBundle';
    }

    /**
     * @deprecated since 6.4. Use the `getEntityClass` method instead.
     *
     * @return string
     */
    public function getEntityName()
    {
        trigger_deprecation('kunstmaan/node-bundle', '6.4', 'The "%s" method is deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.', __METHOD__);

        return 'NodeTranslation';
    }

    public function getEntityClass(): string
    {
        return NodeTranslation::class;
    }

    /**
     * Override path convention (because settings is a virtual admin subtree)
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        if (null === $suffix || $suffix === '') {
            return 'KunstmaanNodeBundle_nodes';
        }

        return sprintf('KunstmaanNodeBundle_nodes_%s', $suffix);
    }

    /**
     * @deprecated since 6.4. There is no replacement for this method.
     *
     * Override controller path (because actions for different entities are
     * defined in a single Settings controller).
     *
     * @return string
     */
    public function getControllerPath()
    {
        trigger_deprecation('kunstmaan/node-bundle', '6.4', 'Method deprecated and will be removed in 7.0. There is no replacement for this method.');

        return 'KunstmaanNodeBundle:NodeAdmin';
    }

    /**
     * @param QueryBuilder $queryBuilder The query builder
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        parent::adaptQueryBuilder($queryBuilder);

        $queryBuilder
            ->select('b,n')
            ->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
            ->andWhere('b.lang = :lang')
            ->andWhere('n.deleted = :deleted')
            ->setParameter('deleted', false)
            ->addOrderBy('b.updated', 'DESC')
            ->setParameter('lang', $this->locale);

        if ($this->node instanceof Node) {
            $queryBuilder->andWhere('n.parent = :parent')
                ->setParameter('parent', $this->node->getId());
        }

        if (!$this->domainConfiguration) {
            return;
        }

        $rootNode = $this->domainConfiguration->getRootNode();
        if (!\is_null($rootNode)) {
            $queryBuilder->andWhere('n.lft >= :left')
                ->andWhere('n.rgt <= :right')
                ->setParameter('left', $rootNode->getLeft())
                ->setParameter('right', $rootNode->getRight());
        }
    }
}
