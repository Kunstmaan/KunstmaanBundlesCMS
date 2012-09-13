<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\Form\AbstractType;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;

abstract class AbstractAdminListConfigurator
{
    /* @var Field[] $fields */
    private $fields = array();
    /* @var Field[] $exportFields */
    private $exportFields = array();
    /* @var ActionInterface[] $customActions */
    private $customActions = array();
    /* @var ListActionInterface[] $listActions */
    private $listActions = array();
    private $type = null;
    private $listTemplate = 'KunstmaanAdminListBundle:Default:list.html.twig';
    private $addTemplate = 'KunstmaanAdminListBundle:Default:add.html.twig';
    private $editTemplate = 'KunstmaanAdminListBundle:Default:edit.html.twig';
    private $deleteTemplate = 'KunstmaanAdminListBundle:Default:delete.html.twig';
    /* @var PermissionDefinition $permissionDefinition */
    private $permissionDefinition = null;

    abstract public function buildFields();

    abstract public function getEditUrlFor($item);

    abstract public function getAddUrlFor($params = array());

    abstract public function getDeleteUrlFor($item);

    abstract public function getIndexUrlFor();

    abstract public function getRepositoryName();

    /**
     * @param entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return AbstractType
     */
    public function getAdminType($entity)
    {
        if (!is_null($this->type)) {
            return $this->type;
        }

        if (method_exists($entity, "getAdminType")) {
            return $entity->getAdminType();
        }

        throw new \InvalidArgumentException("You need to implement the getAdminType method in " . get_class(
            $this
        ) . " or " . get_class($entity));
    }

    /**
     * @param AbstractType $type
     *
     * @return AbstractAdminListConfigurator
     */
    public function setAdminType(AbstractType $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param AdminListFilter $builder
     *
     * @return AbstractAdminListConfigurator
     */
    public function buildFilters(AdminListFilter $builder)
    {
        return $this;
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function buildActions()
    {
        return $this;
    }

    /**
     * @return bool
     */
    public function canEdit()
    {
        return true;
    }

    /**
     * @param string $name
     * @param string $header
     * @param string $sort
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function addField($name, $header, $sort, $template = null)
    {
        $this->fields[] = new Field($name, $header, $sort, $template);

        return $this;
    }

    /**
     * @param string $name
     * @param string $header
     * @param string $sort
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function addExportField($name, $header, $sort, $template = null)
    {
        $this->exportFields[] = new Field($name, $header, $sort, $template);

        return $this;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function canExport()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getExportUrlFor()
    {
        return "";
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return 10;
    }

    /**
     * @return array
     */
    public function getSortFields()
    {
        $array = array();
        foreach ($this->getFields() as $field) {
            if ($field->isSortable()) {
                $array[] = $field->getName();
            }
        }

        return $array;
    }

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Field[]
     */
    public function getExportFields()
    {
        if (empty($this->exportFields)) {
            return $this->fields;
        } else {
            return $this->exportFields;
        }
    }

    /**
     * @param $array
     */
    public function configureListFields(&$array)
    {
        foreach ($this->getFields() as $field) {
            $array[$field->getHeader()] = $field->getName();
        }
    }

    /**
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param array                      $params
     */
    public function adaptQueryBuilder(\Doctrine\ORM\QueryBuilder $queryBuilder, $params = array())
    {
        $queryBuilder->where('1=1');
    }

    /**
     * @param string $label
     * @param string $url
     * @param string $icon
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function addSimpleAction($label, $url, $icon, $template = null)
    {
        $this->customActions[] = new SimpleAction($url, $icon, $label, $template);

        return $this;
    }

    /**
     * @param ActionInterface $customAction
     *
     * @return AbstractAdminListConfigurator
     */
    public function addCustomAction(ActionInterface $customAction)
    {
        $this->customActions[] = $customAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasCustomActions()
    {
        return !empty($this->customActions);
    }

    /**
     * @return ActionInterface[]
     */
    public function getCustomActions()
    {
        return $this->customActions;
    }

    /**
     * @return bool
     */
    public function hasListActions()
    {
        return !empty($this->listActions);
    }

    /**
     * @return ListActionInterface[]
     */
    public function getListActions()
    {
        return $this->listActions;
    }

    /**
     * @param array|object $item
     * @param string       $columnName
     *
     * @return mixed
     */
    public function getValue($item, $columnName)
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

    /**
     * @param array|object $item
     * @param string       $columnName
     *
     * @return string
     */
    public function getStringValue($item, $columnName)
    {
        $result = $this->getValue($item, $columnName);
        if (is_bool($result)) {
            return $result ? "true" : "false";
        }
        if ($result instanceof \DateTime) {
            return $result->format('Y-m-d H:i:s');
        } else {
            if ($result instanceof \Doctrine\ORM\PersistentCollection) {
                $results = "";
                foreach ($result as $entry) {
                    $results[] = $entry->getName();
                }
                if (empty($results)) {
                    return "";
                }

                return implode(', ', $results);
            } else {
                if (is_array($result)) {
                    return implode(', ', $result);
                } else {
                    return $result;
                }
            }
        }
    }

    /**
     * @param ListActionInterface $listAction
     *
     * @return AbstractAdminListConfigurator
     */
    public function addListAction(ListActionInterface $listAction)
    {
        $this->listActions[] = $listAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function useNativeQuery()
    {
        return false;
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $querybuilder
     * @param array                             $params
     *
     * @throws \RuntimeException
     */
    public function adaptNativeCountQueryBuilder($querybuilder, $params = array())
    {
        throw new \RuntimeException('You have to implement the native count query builder!');
    }

    /**
     * @param \Doctrine\DBAL\Query\QueryBuilder $querybuilder
     * @param array                             $params
     *
     * @throws \RuntimeException
     */
    public function adaptNativeItemsQueryBuilder($querybuilder, $params = array())
    {
        throw new \RuntimeException('You have to implement the native items query builder!');
    }

    /**
     * @return string
     */
    public function getListTemplate()
    {
        return $this->listTemplate;
    }

    /**
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function setListTemplate($template)
    {
        $this->listTemplate = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddTemplate()
    {
        return $this->addTemplate;
    }

    /**
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function setAddTemplate($template)
    {
        $this->addTemplate = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getEditTemplate()
    {
        return $this->editTemplate;
    }

    /**
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function setEditTemplate($template)
    {
        $this->editTemplate = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getDeleteTemplate()
    {
        return $this->deleteTemplate;
    }

    /**
     * @param string $template
     *
     * @return AbstractAdminListConfigurator
     */
    public function setDeleteTemplate($template)
    {
        $this->deleteTemplate = $template;

        return $this;
    }

    /**
     * You can override this method to do some custom things you need to do when adding an entity
     *
     * @param object $entity
     */
    public function decorateNewEntity($entity)
    {
        return $entity;
    }

    /**
     * @param PermissionDefinition $permissionDefinition
     *
     * @return AbstractAdminListConfigurator
     */
    public function setPermissionDefinition(PermissionDefinition $permissionDefinition)
    {
        $this->permissionDefinition = $permissionDefinition;

        return $this;
    }

    /**
     * @return PermissionDefinition|null
     */
    public function getPermissionDefinition()
    {
        return $this->permissionDefinition;
    }

}
