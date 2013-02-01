<?php
namespace Kunstmaan\SearchBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Doctrine\ORM\PersistentCollection;
use Kunstmaan\ViewBundle\Entity\SearchPage;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\DateFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;

/**
 * TODO: Fill out the docbook comments
 */
class SearchedForAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{

    /**
     */
    public function buildFilters()
    {
        $builder = $this->getFilterBuilder();
        $builder->add('query', new StringFilterType("query"), "Query")
                ->add('createdat', new DateFilterType('createdat'), "Created At");
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField("query", "Query", true);
        $this->addField("searchpage", "Search Page", false);
        $this->addField("createdat", "Created At", false);
    }


    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanSearchBundle';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'SearchedFor';
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
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array();
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return false;
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array();
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return array('path' => 'KunstmaanSearchBundle_admin_searchedfor');
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return false;
    }

    /**
     * @param mixed $item
     *
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
     * @param \Doctrine\ORM\QueryBuilder $querybuilder The query builder
     * @param array                      $params       Extra parameters
     */
    public function adaptQueryBuilder(\Doctrine\ORM\QueryBuilder $querybuilder, array $params = array())
    {
        parent::adaptQueryBuilder($querybuilder);
        //not needed to change something here yet but already
    }

    /**
     * @param mixed  $item       The item
     * @param string $columnName The column name
     *
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
     * @param mixed $item
     */
    public function getDeleteUrlFor($item)
    {
        // TODO: Implement getDeleteUrlFor() method.
    }
}
