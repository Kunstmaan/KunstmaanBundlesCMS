<?php

namespace {{ namespace }}\AdminList;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;

use {{ namespace }}\Entity\{{ entity_class }};

/**
 * The {{ entity_class }} admin list configurator
 */
class {{ entity_class }}AdminListConfigurator extends AbstractAdminListConfigurator {

    private $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFields() {
        {%- for field in fields %}
        $this->addField('{{ field }}', '{{ field }}', true);
    
    
        {%- endfor %}
    }
    
    /**
	 * {@inheritdoc}
	 */
	public function buildFilters(AdminListFilter $builder) {
		{%- for field in fields %}
			$builder->add('{{ field }}', new StringFilterType('{{ field }}'), '{{ field }}');


        {%- endfor %}
	}

    /**
	 * {@inheritdoc}
	 */
	public function getRepositoryName()
	{
		return '{{ bundle.getName() }}:{{ entity_class }}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIndexUrlFor()
	{
	    return array('path' => '{{ bundle.getName() }}_{{ entity_class }}', 'params' => array());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getEditUrlFor($item)
	{
	    return array('path' => '{{ bundle.getName() }}_{{ entity_class }}_edit', 'params' => array('id' => $item->getId()));
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canAdd()
	{
	    return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getAddUrlFor($params = array())
	{
	    return array('path' => '{{ bundle.getName() }}_{{ entity_class }}_add', 'params' => array());
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function canDelete($item)
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function getDeleteUrlFor($item)
	{
	    return array('action' => '{{ bundle.getName() }}:{{ entity_class }}:delete', 'path' => '{{ bundle.getName() }}_{{ entity_class }}_delete', 'params' => array('id' => $item->getId()));
	}
	
	/**
	 * @return string
	 */
	public function canExport() {
	    return true;
	}
	
	/**
	 *
	 */
	public function getExportUrlFor() {
	    return array('path' => '{{ bundle.getName() }}_{{ entity_class }}_export', 'params' => array('_format' => 'csv'));
	}

	/**
	 * {@inheritdoc}
	 */
    public function adaptQueryBuilder($querybuilder, $params=array())
    {
        parent::adaptQueryBuilder($querybuilder);
		return $querybuilder;
	}

}