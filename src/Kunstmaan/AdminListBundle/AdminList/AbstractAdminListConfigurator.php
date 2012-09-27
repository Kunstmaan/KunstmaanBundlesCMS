<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Pagerfanta\Pagerfanta;

abstract class AbstractAdminListConfigurator implements AdminListConfiguratorInterface
{
    const SUFFIX_ADD = 'add';
    const SUFFIX_EDIT = 'edit';
    const SUFFIX_EXPORT = 'export';
    const SUFFIX_DELETE = 'delete';

    /* @var Field[] $fields */
    private $fields = array();

    /* @var Field[] $exportFields */
    private $exportFields = array();

    /* @var ActionInterface[] $customActions */
    private $customActions = array();

    /* @var ListActionInterface[] $listActions */
    private $listActions = array();

    /* @var AbstractType $type */
    private $type = null;

    /* @var string $listTemplate */
    private $listTemplate = 'KunstmaanAdminListBundle:Default:list.html.twig';

    /* @var string $addTemplate */
    private $addTemplate = 'KunstmaanAdminListBundle:Default:add.html.twig';

    /* @var string $editTemplate */
    private $editTemplate = 'KunstmaanAdminListBundle:Default:edit.html.twig';

    /* @var string $deleteTemplate */
    private $deleteTemplate = 'KunstmaanAdminListBundle:Default:delete.html.twig';

    /* @var AdminListFilter $adminListFilter */
    private $adminListFilter = null;

    /* @var int $page */
    protected $page = 1;

    /* @var string $orderBy */
    protected $orderBy = '';

    /* @var string $orderDirection */
    protected $orderDirection = '';

    /**
     * Configure the visible columns
     */
    abstract public function buildFields();

    /**
     * Return the url to edit the given $item
     *
     * @param object|array $item
     *
     * @return array
     */
    abstract public function getEditUrlFor($item);

    /**
     * Configure the types of items you can add
     *
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array())
    {
        return array(
            strtolower($this->getEntityName()) => array('path' => $this->getPathByConvention($this::SUFFIX_ADD),
                                                        'params' => $params)
        );
    }

    /**
     * Get the delete url for the given $item
     *
     * @param object|array $item
     *
     * @return array
     */
    abstract public function getDeleteUrlFor($item);

    /**
     * Return the url to list all the items
     *
     * @param array $params
     *
     * @return array
     */
    public function getIndexUrlFor(array $params = array())
    {
        return array(
            'path' => $this->getPathByConvention(),
            'params' => $params
        );
    }

    /**
     * @return Pagerfanta
     */
    abstract public function getPagerfanta();

    /**
     * Return current bundle name.
     *
     * @return string
     */
    abstract public function getBundleName();

    /**
     * Return current entity name.
     *
     * @return string
     */
    abstract public function getEntityName();

    /**
     * Return default repository name.
     *
     * @return string
     */
    public function getRepositoryName()
    {
        return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
    }

    /**
     * @param object $entity
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
     * Configure the fields you can filter on
     */
    public function buildFilters()
    {
    }

    /**
     * configure the actions for each line
     */
    public function buildActions()
    {
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
     * @param string $name     The field name
     * @param string $header   The header title
     * @param string $sort     Sortable column or not
     * @param string $template The template
     *
     * @return AbstractAdminListConfigurator
     */
    public function addField($name, $header, $sort, $template = null)
    {
        $this->fields[] = new Field($name, $header, $sort, $template);

        return $this;
    }

    /**
     * @param string $name     The field name
     * @param string $header   The header title
     * @param string $template The template
     *
     * @return AbstractAdminListConfigurator
     */
    public function addExportField($name, $header, $template = null)
    {
        $this->exportFields[] = new Field($name, $header, false, $template);

        return $this;
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
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canExport()
    {
        return false;
    }

    /**
     * Get the url to export the listed items
     *
     * @return string
     */
    public function getExportUrlFor()
    {
        return '';
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
     * @param string $label    The label, only used when the template equals null
     * @param string $url      The action url
     * @param string $icon     The icon, only used when the template equals null
     * @param string $template The template, when not specified the label is shown
     *
     * @return AbstractAdminListConfigurator
     */
    public function addSimpleAction($label, $url, $icon, $template = null)
    {
        return $this->addCustomAction(new SimpleAction($url, $icon, $label, $template));
    }

    /**
     * @param ListActionInterface $customAction
     *
     * @return AbstractAdminListConfigurator
     */
    public function addCustomAction(ListActionInterface $customAction)
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
     * @param array|object $item       The item
     * @param string       $columnName The column name
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
            }
        }
        $methodName = $columnName;
        if (method_exists($item, $methodName)) {
            $result = $item->$methodName();
        } else {
            $methodName = 'get' . $columnName;
            if (method_exists($item, $methodName)) {
                $result = $item->$methodName();
            } else {
                $methodName = 'is' . $columnName;
                if (method_exists($item, $methodName)) {
                    $result = $item->$methodName();
                } else {
                    $methodName = 'has' . $columnName;
                    if (method_exists($item, $methodName)) {
                        $result = $item->$methodName();
                    } else {
                        return sprintf('undefined function [get/is/has]%s()', $columnName);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return string
     */
    public function getStringValue($item, $columnName)
    {
        $result = $this->getValue($item, $columnName);
        if (is_bool($result)) {
            return $result ? 'true' : 'false';
        }
        if ($result instanceof \DateTime) {
            // @todo Get rid of hardcoded date format below?
            return $result->format('Y-m-d H:i:s');
        } else {
            if ($result instanceof \Doctrine\ORM\PersistentCollection) {
                $results = "";
                foreach ($result as $entry) {
                    // @todo Check where this is used, a PersistentCollection doesn't always have entities with a name property!!
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
     * @return AdminListConfiguratorInterface
     */
    public function addListAction(ListActionInterface $listAction)
    {
        $this->listActions[] = $listAction;

        return $this;
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
     * @return AdminListConfiguratorInterface
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
     * @return AdminListConfiguratorInterface
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
     * @return AdminListConfiguratorInterface
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
     * @return AdminListConfiguratorInterface
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
     *
     * @return mixed
     */
    public function decorateNewEntity($entity)
    {
        return $entity;
    }

    /**
     * @return AdminListFilter
     */
    public function getAdminListFilter()
    {
        if (is_null($this->adminListFilter)) {
            $this->adminListFilter = new AdminListFilter();
        }

        return $this->adminListFilter;
    }

    /**
     * Bind current request.
     *
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->page = $request->query->get('page');
        if (is_null($this->page)) {
            $this->page = 1;
        }
        if (!is_null($request->query->get('orderBy'))) {
            $this->orderBy = $request->query->get('orderBy');
        }
        if (!is_null($request->query->get('orderDirection'))) {
            $this->orderDirection = $request->query->get('orderDirection');
        }
        $this->adminListFilter->bindRequest($request);
    }

    /**
     * Return current page.
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Return current sorting column.
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * Return current sorting direction.
     *
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        if (empty($suffix)) {
            return sprintf('%s_admin_%ss', $this->getBundleName(), strtolower($this->getEntityName()));
        }

        return sprintf('%s_admin_%ss_%s', $this->getBundleName(), strtolower($this->getEntityName()), $suffix);
    }

    /**
     * Get controller path.
     *
     * @return string
     */
    public function getControllerPath()
    {
        return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
    }

}
