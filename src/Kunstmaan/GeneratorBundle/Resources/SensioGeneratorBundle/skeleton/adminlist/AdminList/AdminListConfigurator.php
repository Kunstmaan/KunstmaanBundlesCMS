<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;

use {{ namespace }}\Form\{{ entity_class }}AdminType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
{% if generate_admin_type %}
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
{% endif %}
use Kunstmaan\AdminListBundle\AdminList\SortableInterface;

class {{ entity_class }}AdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator{% if sortField %} implements SortableInterface {% endif %}
{
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
{% if generate_admin_type %}
        $this->setAdminType({{ entity_class }}AdminType::class);
{% endif %}
    }

    public function buildFields()
    {
{% for fieldName, data in fields %}
        $this->addField('{{ fieldName }}', '{{ data.fieldTitle }}', true);
{% endfor %}
    }

    public function buildFilters()
    {
{% for fieldName, data in fields %}
        $this->addFilter('{{ fieldName }}', new {{ data.filterType }}('{{ fieldName }}'), '{{ data.fieldTitle }}');
{% endfor %}
    }

    public function getBundleName(): string
    {
        return '{{ bundle.getName() }}';
    }

    public function getEntityName(): string
    {
        return '{{ entity_class }}';
    }
{% if sortField %}

    public function getSortableField(): string
    {
        return "{{ sortField }}";
    }
{% endif %}
}
