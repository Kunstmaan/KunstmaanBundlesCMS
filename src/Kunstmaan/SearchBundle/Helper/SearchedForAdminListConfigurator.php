<?php
namespace Kunstmaan\SearchBundle\Helper;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Doctrine\ORM\PersistentCollection;
use Kunstmaan\ViewBundle\Entity\SearchPage;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

/**
 * TODO: Fill out the docbook comments
 */
class SearchedForAdminListConfigurator extends AbstractAdminListConfigurator
{

    /**
     * @param \Kunstmaan\AdminListBundle\AdminList\AdminListFilter $builder
     */
    public function buildFilters(AdminListFilter $builder)
    {
        $builder->add('query', new StringFilterType("query"), "Query");
        $builder->add('createdat', new DateFilterType('createdat'), "Created At");
    }

    public function buildFields()
    {
        $this->addField("query", "Query", true);
        $this->addField("searchpage", "Search Page", false);
        $this->addField("createdat", "Created At", false);
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return false;
    }

    /**
     * @param array $params
     * @return array
     */
    public function getAddUrlFor($params = array())
    {
        return array();
    }

    /**
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     * @param $item
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array();
    }

    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanAdminBundle_settings_searches');
    }

    /**
     * @param $item
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @param $item
     * @return null
     */
    public function getAdminType($item)
    {
        return null;
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return 'KunstmaanSearchBundle:SearchedFor';
    }

    /**
     * @param $querybuilder
     * @param array $params
     */
    public function adaptQueryBuilder($querybuilder, $params = array())
    {
        parent::adaptQueryBuilder($querybuilder);
        //not needed to change something here yet but already
    }

    /**
     * @param $item
     * @param $columnName
     * @return string
     */
    public function getValue($item, $columnName)
    {
        $result = parent::getValue($item, $columnName);
        if ($result instanceof SearchPage) {
            /** @var SearchPage $result */
            $parent = $result->getParent();
            /** @var HasNodeInterface $parent */
            if ($parent) {
                return $parent->getTitle() . "/" . $result->getTitle();
            } else {
                return "/" . $result->getTitle();
            }
        }
        if ($result instanceof PersistentCollection) {
            $results = "";
            foreach ($result as $entry) {
                $results[] = $entry->getName();
            }
            if (empty($results)) {
                return "";
            }

            return implode(', ', $results);
        }

        return $result;
    }

    /**
     * @param $item
     */
    function getDeleteUrlFor($item)
    {
        // TODO: Implement getDeleteUrlFor() method.
    }
}
