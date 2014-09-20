<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;

{% if generate_admin_type %}
{{ namespace }}\Form\{{ entity_class }}AdminType;
{% endif %}

/**
 * The admin list configurator for {{ entity_class }}
 */
class {{ entity_class }}AdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
{% if generate_admin_type %}
        $this->setAdminType(new {{ entity_class }}AdminType());
{% endif %}
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
{% for fieldName, data in fields %}
        $this->addField('{{ fieldName }}', '{{ data.fieldTitle }}', true);
{% endfor %}
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
{% for fieldName, data in fields %}
        $this->addFilter('{{ fieldName }}', new {{ data.filterType }}('{{ fieldName }}'), '{{ data.fieldTitle }}');
{% endfor %}
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return '{{ bundle.getName() }}';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return '{{ entity_class }}';
    }

}
