<?php

namespace Kunstmaan\RedirectBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\RedirectBundle\Form\RedirectAdminType;

class RedirectAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     * @param DomainConfigurationInterface $domainConfiguration
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null, DomainConfigurationInterface $domainConfiguration)
    {
        parent::__construct($em, $aclHelper);

        $this->domainConfiguration = $domainConfiguration;
        $this->setAdminType(new RedirectAdminType($domainConfiguration));
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $this->addField('domain', 'Domain', true);
        }
        $this->addField('origin', 'Origin', true);
        $this->addField('target', 'Target', true);
        $this->addField('permanent', 'Permanent?', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $hosts = $this->domainConfiguration->getHosts();
            $domains = array_combine($hosts, $hosts);
            $domains = array_merge(array('' => 'redirect.all'), $domains);
            $this->addFilter('domain', new ORM\EnumerationFilterType('domain'), 'Domain', $domains);
        }
        $this->addFilter('origin', new ORM\StringFilterType('origin'), 'Origin');
        $this->addFilter('target', new ORM\StringFilterType('target'), 'Target');
        $this->addFilter('permanent', new ORM\BooleanFilterType('permanent'), 'Permanent');
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return string
     */
    public function getValue($item, $columnName)
    {
        if ($columnName == 'domain' && !$item->getDomain()) {
            return 'All domains';
        }

        return parent::getValue($item, $columnName);
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanRedirectBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Redirect';
    }
}
