<?php

namespace Kunstmaan\LeadGenerationBundle\AdminList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;

class RulesAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @var int
     */
    protected $popupId;

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     * @param int           $id        The if of the popup
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null, $id)
    {
        parent::__construct($em, $aclHelper);

        $this->setPopupId($id);
        $this->setListTemplate('KunstmaanLeadGenerationBundle:AdminList:rules-list.html.twig');
        $this->setEditTemplate('KunstmaanLeadGenerationBundle:AdminList:rules-edit.html.twig');
        $this->setAddTemplate('KunstmaanLeadGenerationBundle:AdminList:rules-edit.html.twig');
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param array        $params
     */
    public function adaptQueryBuilder(QueryBuilder $queryBuilder, array $params = array())
    {
        $queryBuilder->where('b.popup = :id');
        $queryBuilder->setParameter('id', $this->getPopupId());
        $queryBuilder->orderBy('b.id', 'ASC');
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        return array(
            'path' => 'kunstmaanleadgenerationbundle_admin_rule_abstractrule_detail',
            'params' => array('popup' => $this->getPopupId()),
        );
    }

    /**
     * Get the edit url for the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        $params = array('id' => $item->getId(), 'popup' => $this->getPopupId());
        $params = array_merge($params, $this->getExtraParameters());

        return array(
            'path' => 'kunstmaanleadgenerationbundle_admin_rule_abstractrule_edit',
            'params' => $params,
        );
    }

    /**
     * Get the delete url for the given $item
     *
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        $params = array('id' => $item->getId(), 'popup' => $this->getPopupId());
        $params = array_merge($params, $this->getExtraParameters());

        return array(
            'path' => 'kunstmaanleadgenerationbundle_admin_rule_abstractrule_delete',
            'params' => $params,
        );
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('id', 'kuma_lead_generation.rules.list.header.id', true);
        $this->addField('classname', 'kuma_lead_generation.rules.list.header.type', false);
        $this->addField('jsProperties', 'kuma_lead_generation.rules.list.header.properties', false);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('id', new ORM\StringFilterType('id'), 'kuma_lead_generation.rules.list.filter.id');
    }

    public function getValue($item, $columnName)
    {
        if ($columnName == 'jsProperties') {
            return json_encode($item->getJsProperties());
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
        return 'KunstmaanLeadGenerationBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Rule\AbstractRule';
    }

    /**
     * @param object $entity
     *
     * @return object
     */
    public function decorateNewEntity($entity)
    {
        $entity->setPopup($this->getPopup());

        return $entity;
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

    /**
     * @return int
     */
    public function getPopupId()
    {
        return $this->popupId;
    }

    /**
     * @param int $popupId
     */
    public function setPopupId($popupId)
    {
        $this->popupId = $popupId;
    }

    /**
     * @return AbstractPopup
     */
    public function getPopup()
    {
        return $this->em->getRepository('KunstmaanLeadGenerationBundle:Popup\AbstractPopup')->find($this->getPopupId());
    }
}
