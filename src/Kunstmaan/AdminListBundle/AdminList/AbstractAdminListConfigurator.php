<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Doctrine\ORM\QueryBuilder as ORMQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder as DBALQueryBuilder;

abstract class AbstractAdminListConfigurator
{

    private $fields = array();
    private $exportFields = array();
    private $customActions = array();
    private $listActions = array();

    abstract function buildFields();
    abstract function getEditUrlFor($item);
    abstract function getAddUrlFor($params = array());
    abstract function getDeleteUrlFor($item);
    abstract function getIndexUrlFor();
    abstract function getRepositoryName();

    public function buildFilters(AdminListFilter $builder)
    {
    }
    
    public function buildActions()
    {
    }

    public function canEdit()
    {
        return true;
    }

    function addField($fieldname, $fieldheader, $sort, $template = null)
    {
        $this->fields[] = new Field($fieldname, $fieldheader, $sort, $template);
    }

    function addExportField($fieldname, $fieldheader, $sort, $template = null)
    {
        $this->exportFields[] = new Field($fieldname, $fieldheader, $sort, $template);
    }

    public function canDelete($item)
    {
        return true;
    }

    public function canAdd()
    {
        return true;
    }

    public function canExport()
    {
        return false;
    }

    public function getExportUrlFor()
    {
        return "";
    }

    function getLimit()
    {
        return 10;
    }

    function getSortFields()
    {
        $array = array();
        foreach ($this->getFields() as $field) {
            if ($field->isSortable())
                $array[] = $field->getFieldname();
        }
        return $array;
    }

    function getFields()
    {
        return $this->fields;
    }

    function getExportFields()
    {
        if (empty($this->exportFields)) {
            return $this->fields;
        } else {
            return $this->exportFields;
        }
    }

    function configureListFields(&$array)
    {
        foreach ($this->getFields() as $field) {
            $array[$field->getFieldheader()] = $field->getFieldname();
        }
    }

    function adaptQueryBuilder(ORMQueryBuilder $querybuilder, $params = array())
    {
        $querybuilder->where('1=1');
    }

    public function addSimpleAction($label, $url, $icon, $template = null)
    {
        $this->customActions[] = new SimpleAction($url, $icon, $label, $template);
    }

    public function hasCustomActions()
    {
        return !empty($this->customActions);
    }

    public function getCustomActions()
    {
        return $this->customActions;
    }

    public function hasListActions()
    {
        return !empty($this->listActions);
    }

    public function getListActions()
    {
        return $this->listActions;
    }

    function getValue($item, $columnName)
    {
        if (is_array($item)) {
            if (isset($item[$columnName])) {
                return $item[$columnName];
            } else {
                return '';
                // return sprintf("undefined column %s", $columnName);
            }
        }
        $methodName = $columnName;
        if (method_exists($item, $methodName)) {
            $result = $item->$methodName();
        } else {
            $methodName = "get" . $columnName;
            if (method_exists($item, $methodName)) {
                $result = $item->$methodName();
            } else {
                $methodName = "is" . $columnName;
                if (method_exists($item, $methodName)) {
                    $result = $item->$methodName();
                } else {
                    $methodName = "has" . $columnName;
                    if (method_exists($item, $methodName)) {
                        $result = $item->$methodName();
                    } else {
                        return sprintf("undefined function [get/is/has]%s()", $columnName);
                    }
                }
            }
        }
        return $result;
    }

    function getStringValue($item, $columnName)
    {
        $result = $this->getValue($item, $columnName);
        if (is_bool($result)) {
            return $result ? "true" : "false";
        }
        if ($result instanceof \DateTime) {
            return $result->format('Y-m-d H:i:s');
        } else if ($result instanceof \Doctrine\ORM\PersistentCollection) {
            $results = "";
            foreach ($result as $entry) {
                $results[] = $entry->getName();
            }
            if (empty($results)) {
                return "";
            }
            return implode(', ', $results);
        } else if (is_array($result)) {
            return implode(', ', $result);
        } else {
            return $result;
        }
    }

    public function addListAction(ListActionInterface $listAction)
    {
        $this->listActions[] = $listAction;
    }
    
    public function useNativeQuery()
    {
        return false;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $querybuilder
     * @param array                             $params
     *
     * @throws \Exception
     */
    function adaptNativeCountQueryBuilder(DBALQueryBuilder $querybuilder, $params = array())
    {
        throw new \Exception('You have to implement the native count query builder!');
    }
    
    function adaptNativeItemsQueryBuilder(DBALQueryBuilder $querybuilder, $params = array())
    {
        throw new \Exception('You have to implement the native items query builder!');
    }
    
}
