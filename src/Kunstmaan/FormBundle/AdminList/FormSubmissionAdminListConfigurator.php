<?php

namespace Kunstmaan\FormBundle\AdminList;

use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

/**
 * The form submssions admin list configurator
 */
class FormSubmissionAdminListConfigurator extends AbstractAdminListConfigurator
{

	protected $nodeTranslation;

	/**
	 * @param NodeTranslation $nodeTranslation
	 */
	public function __construct($nodeTranslation)
	{
		$this->nodeTranslation = $nodeTranslation;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildFilters(AdminListFilter $builder)
	{
		$builder->add('created', new DateFilterType("created"), "Date");
		$builder->add('lang', new BooleanFilterType("lang"), "Language");
		$builder->add('ipAddress', new StringFilterType("ipAddress"), "IP Address");
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildFields()
	{
		$this->addField("created", "Date", true);
		$this->addField("lang", "Language", true);
		$this->addField("ipAddress", "ipAddress", true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEditUrlFor($item)
	{
		return array('path' => 'KunstmaanFormBundle_formsubmissions_list_edit', 'params' => array('nodetranslationid' => $this->nodeTranslation->getId(), 'submissionid' => $item->getId()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getIndexUrlFor()
	{
	    return array('path' => 'KunstmaanFormBundle_formsubmissions_list', 'params' => array('nodetranslationid' => $this->nodeTranslation->getId()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function canAdd()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAddUrlFor($params = array())
	{
		return "";
	}

	/**
	 * {@inheritdoc}
	 */
	public function canDelete($item)
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRepositoryName()
	{
		return 'KunstmaanFormBundle:FormSubmission';
	}

	/**
	 * {@inheritdoc}
	 */
    public function adaptQueryBuilder($querybuilder, $params=array())
    {
        parent::adaptQueryBuilder($querybuilder);
        $querybuilder->innerJoin('b.node', 'n', 'WITH', 'b.node = n.id')
	        ->andWhere('n.id = ?1')
	        ->setParameter(1, $this->nodeTranslation->getNode()->getId())
	        ->addOrderBy('b.created', 'DESC');

		return $querybuilder;
	}

	/**
	 * {@inheritdoc}
	 */
    public function getDeleteUrlFor($item)
    {
        return array();
    }


}
