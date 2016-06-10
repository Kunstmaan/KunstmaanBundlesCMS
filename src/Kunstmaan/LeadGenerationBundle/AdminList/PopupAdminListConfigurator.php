<?php

namespace Kunstmaan\LeadGenerationBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

class PopupAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em The entity manager
     * @param AclHelper $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);

        $this->setListTemplate('KunstmaanLeadGenerationBundle:AdminList:popup-list.html.twig');
        $this->setEditTemplate('KunstmaanLeadGenerationBundle:AdminList:popup-edit.html.twig');
        $this->setAddTemplate('KunstmaanLeadGenerationBundle:AdminList:popup-edit.html.twig');
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('id', 'kuma_lead_generation.popup.list.header.id', true);
        $this->addField('name', 'kuma_lead_generation.popup.list.header.name', true);
        $this->addField('classname', 'kuma_lead_generation.popup.list.header.type', false);
        $this->addField('htmlId', 'kuma_lead_generation.popup.list.header.html_id', true);
        $this->addField('ruleCount', 'kuma_lead_generation.popup.list.header.rule_count', false);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('name', new ORM\StringFilterType('name'), 'kuma_lead_generation.popup.list.filter.name');
        $this->addFilter('htmlId', new ORM\StringFilterType('htmlId'), 'kuma_lead_generation.popup.list.filter.html_id');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanLeadGenerationBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Popup\AbstractPopup';
    }

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return true;
    }

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param object|array $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return true;
    }

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }
}
