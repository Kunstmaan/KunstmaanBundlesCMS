<?php

namespace Kunstmaan\RedirectBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Kunstmaan\RedirectBundle\Form\RedirectAdminType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

/**
 * The admin list configurator for Redirect
 */
class RedirectAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new RedirectAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('origin', 'Origin', true);
        $this->addField('target', 'Target', true);
        $this->addField('permanent', 'Permanent?', true);

    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('origin', new ORM\StringFilterType('origin'), 'Origin');
        $this->addFilter('target', new ORM\StringFilterType('target'), 'Target');
        $this->addFilter('permanent', new ORM\BooleanFilterType('permanent'), 'Permanent');
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
