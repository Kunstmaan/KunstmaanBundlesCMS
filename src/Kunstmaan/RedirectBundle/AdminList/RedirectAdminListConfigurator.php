<?php

namespace Kunstmaan\RedirectBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\RedirectBundle\Form\RedirectAdminType;

class RedirectAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param EntityManager                $em                  The entity manager
     * @param AclHelper                    $aclHelper           The acl helper
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(
        EntityManager $em,
        AclHelper $aclHelper = null,
        DomainConfigurationInterface $domainConfiguration
    ) {
        parent::__construct(
            $em,
            $aclHelper
        );

        $this->domainConfiguration = $domainConfiguration;

        $this->setAdminType(RedirectAdminType::class);
        $this->setAdminTypeOptions(['domainConfiguration' => $domainConfiguration]);
    }

    /**
     * Configure the visible columns
     */
    public function buildFields(): void
    {
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $this->addField(
                'domain',
                'redirect.adminlist.header.domain',
                true
            );
        }
        $this->addField(
            'origin',
            'redirect.adminlist.header.origin',
            true
        );
        $this->addField(
            'target',
            'redirect.adminlist.header.target',
            true
        );
        $this->addField(
            'permanent',
            'redirect.adminlist.header.permanent',
            true
        );
        $this->addField(
            'note',
            'redirect.adminlist.header.note',
            true
        );
        $this->addField(
            'isAutoRedirect',
            'redirect.adminlist.header.is_auto_redirect',
            true
        );
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters(): void
    {
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $hosts = $this->domainConfiguration->getHosts();
            $domains = array_combine(
                $hosts,
                $hosts
            );
            $domains = array_merge(
                ['' => 'redirect.all'],
                $domains
            );
            $this->addFilter(
                'domain',
                new ORM\EnumerationFilterType('domain'),
                'redirect.adminlist.filter.domain',
                $domains
            );
        }

        $this->addFilter(
            'origin',
            new ORM\StringFilterType('origin'),
            'redirect.adminlist.filter.origin'
        );
        $this->addFilter(
            'target',
            new ORM\StringFilterType('target'),
            'redirect.adminlist.filter.target'
        );
        $this->addFilter(
            'permanent',
            new ORM\BooleanFilterType('permanent'),
            'redirect.adminlist.filter.permanent'
        );
        $this->addFilter(
            'note',
            new ORM\StringFilterType('note'),
            'redirect.adminlist.filter.note'
        );
        $this->addFilter(
            'isAutoRedirect',
            new ORM\BooleanFilterType('isAutoRedirect'),
            'redirect.adminlist.filter.is_auto_redirect'
        );
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return string
     */
    public function getValue(
        $item,
        $columnName
    ) {
        if ($columnName === 'domain' && !$item->getDomain()) {
            return 'All domains';
        }

        if ($columnName === 'isAutoRedirect' && $item->isAutoRedirect() === null) {
            return false;
        }

        return parent::getValue(
            $item,
            $columnName
        );
    }

    public function getBundleName(): string
    {
        return 'KunstmaanRedirectBundle';
    }

    public function getEntityName(): string
    {
        return 'Redirect';
    }
}
