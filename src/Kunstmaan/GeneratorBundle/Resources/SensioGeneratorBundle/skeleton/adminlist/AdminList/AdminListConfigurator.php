<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;

{% if generate_admin_type %}
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
{% endif %}
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;
use {{ namespace }}\Form\{{ entity_class }}AdminType;

/**
 * The admin list configurator for {{ entity_class }}
 */
class {{ entity_class }}AdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator {% if sortField %}implements SortableInterface {% endif %}
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
{% if generate_admin_type %}
        $this->setAdminType({{ entity_class }}AdminType::class);
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

{% if sortField %}
    /**
     * Get sortable field name
     *
     * @return string
     */
    public function getSortableField()
    {
        return "{{ sortField }}";
    }
{% endif %}

}
