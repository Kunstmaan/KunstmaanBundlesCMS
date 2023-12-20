<?php

namespace Kunstmaan\CookieBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\EnumerationFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\CookieBundle\Entity\CookieType;
use Kunstmaan\CookieBundle\Form\CookieAdminType;

/**
 * Class CookieAdminListConfigurator
 */
class CookieAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator implements OverviewNavigationInterface
{
    /**
     * @var DomainConfigurationInterface
     */
    private $domainConfiguration;

    public function __construct(EntityManager $em, AclHelper $aclHelper = null, DomainConfigurationInterface $domainConfiguration = null)
    {
        parent::__construct($em, $aclHelper);

        $this->domainConfiguration = $domainConfiguration;

        $this->setAdminType(CookieAdminType::class);
        $this->setAdminTypeOptions(['domainConfiguration' => $domainConfiguration]);
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('name', 'kuma.cookie.adminlists.cookie.name', true);
        $this->addField('t.name', 'kuma.cookie.adminlists.cookie.type', true, null, new FieldAlias('t', 'type'));
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $this->addField('domain', 'kuma.cookie.adminlists.header.domain', true);
        }
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new StringFilterType('name'), 'kuma.cookie.adminlists.cookie.name');
        $this->addFilter('type', new EnumerationFilterType('id', 't'), 'kuma.cookie.adminlists.cookie.type', $this->getCookieTypes());
        if ($this->domainConfiguration->isMultiDomainHost()) {
            $hosts = $this->domainConfiguration->getHosts();
            $domains = array_combine($hosts, $hosts);
            $domains = array_merge(['' => 'kuma.cookie.adminlists.filter.all'], $domains);
            $this->addFilter('domain', new EnumerationFilterType('domain'), 'kuma.cookie.adminlist.filter.domain', $domains);
        }
    }

    public function adaptQueryBuilder(QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addSelect('t')
            ->innerJoin('b.type', 't');
    }

    /**
     * @return array
     */
    private function getCookieTypes()
    {
        $cookieTypes = [];
        foreach ($this->em->getRepository(CookieType::class)->findAll() as $cookieType) {
            $cookieTypes[$cookieType->getId()] = $cookieType->getName();
        }

        return $cookieTypes;
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
        return 'KunstmaanCookieBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Cookie';
    }

    /**
     * @return string
     */
    public function getOverViewRoute()
    {
        return 'kunstmaancookiebundle_admin_cookie';
    }
}
