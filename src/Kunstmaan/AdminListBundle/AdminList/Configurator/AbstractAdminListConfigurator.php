<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Doctrine\ORM\PersistentCollection;
use InvalidArgumentException;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\BulkActionInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\AdminListBundle\AdminList\FieldAlias;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\FilterType\FilterTypeInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use Kunstmaan\AdminListBundle\AdminList\ListAction\ListActionInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Abstract admin list configurator, this implements the most common functionality from the
 * AdminListConfiguratorInterface and ExportListConfiguratorInterface
 */
abstract class AbstractAdminListConfigurator implements AdminListConfiguratorInterface, ExportListConfiguratorInterface
{
    const SUFFIX_ADD = 'add';
    const SUFFIX_EDIT = 'edit';
    const SUFFIX_EXPORT = 'export';
    const SUFFIX_DELETE = 'delete';
    const SUFFIX_VIEW = 'view';

    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * @var Field[]
     */
    private $exportFields = [];

    /**
     * @var ItemActionInterface[]
     */
    private $itemActions = [];

    /**
     * @var ListActionInterface[]
     */
    private $listActions = [];

    /**
     * @var BulkActionInterface[]
     */
    private $bulkActions = [];

    /**
     * @var AbstractType
     */
    private $type;

    /**
     * @var array
     */
    private $typeOptions = [];

    /**
     * @var string
     */
    private $listTemplate = '@KunstmaanAdminList/Default/list.html.twig';

    /**
     * @var string
     */
    private $addTemplate = '@KunstmaanAdminList/Default/add_or_edit.html.twig';

    /**
     * @var string
     */
    private $editTemplate = '@KunstmaanAdminList/Default/add_or_edit.html.twig';

    /**
     * @var string
     */
    private $viewTemplate = '@KunstmaanAdminList/Default/view.html.twig';

    /**
     * @var string
     */
    private $deleteTemplate = '@KunstmaanAdminList/Default/delete.html.twig';

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var string
     */
    protected $orderBy = '';

    /**
     * @var string
     */
    protected $orderDirection = '';

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
     * Configure the fields you can filter on
     */
    public function buildFilters()
    {
    }

    /**
     * Configure the actions for each line
     */
    public function buildItemActions()
    {
    }

    /**
     * Configure the actions that can be executed on the whole list
     */
    public function buildListActions()
    {
    }

    /**
     * Configure the export fields
     */
    public function buildExportFields()
    {
        /*
         * This is only here to prevent a BC break!!!
         *
         * Just override this function if you want to set your own fields...
         */
        if (empty($this->fields)) {
            $this->buildFields();
        }
    }

    /**
     * Build iterator (if needed)
     */
    public function buildIterator()
    {
    }

    /**
     * Reset all built members
     */
    public function resetBuilds()
    {
        $this->fields = [];
        $this->exportFields = [];
        $this->filterBuilder = null;
        $this->itemActions = [];
        $this->listActions = [];
    }

    /**
     * Configure the types of items you can add
     *
     * @return array
     */
    public function getAddUrlFor(array $params = [])
    {
        $params = array_merge($params, $this->getExtraParameters());

        $friendlyName = explode('\\', $this->getEntityName());
        $friendlyName = array_pop($friendlyName);
        $re = '/(?<=[a-z])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/';
        $a = preg_split($re, $friendlyName);
        $superFriendlyName = implode(' ', $a);

        return [
            $superFriendlyName => [
                'path' => $this->getPathByConvention($this::SUFFIX_ADD),
                'params' => $params,
            ],
        ];
    }

    /**
     * Get the url to export the listed items
     *
     * @return array
     */
    public function getExportUrl()
    {
        $params = $this->getExtraParameters();

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_EXPORT),
            'params' => array_merge(['_format' => 'csv'], $params),
        ];
    }

    /**
     * Get the view url for the given $item
     *
     * @param object|array $item
     *
     * @return array
     */
    public function getViewUrlFor($item)
    {
        if (\is_object($item)) {
            $id = $item->getid();
        } else {
            $id = $item['id'];
        }
        $params = ['id' => $id];
        $params = array_merge($params, $this->getExtraParameters());

        return [
            'path' => $this->getPathByConvention($this::SUFFIX_VIEW),
            'params' => $params,
        ];
    }

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrl()
    {
        $params = $this->getExtraParameters();

        return [
            'path' => $this->getPathByConvention(),
            'params' => $params,
        ];
    }

    /**
     * @param object $entity
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    public function getAdminType($entity)
    {
        if (null !== $this->type) {
            return $this->type;
        }

        if (method_exists($entity, 'getAdminType')) {
            return $entity->getAdminType();
        }

        throw new InvalidArgumentException('You need to implement the getAdminType method in ' . \get_class($this) . ' or ' . \get_class($entity));
    }

    /**
     * @param string $type
     *
     * @return AbstractAdminListConfigurator
     */
    public function setAdminType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param array $typeOptions
     *
     * @return AbstractAdminListConfigurator
     */
    public function setAdminTypeOptions($typeOptions)
    {
        $this->typeOptions = $typeOptions;

        return $this;
    }

    /**
     * Return the default form admin type options
     *
     * @return array
     */
    public function getAdminTypeOptions()
    {
        return $this->typeOptions;
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

    public function canView($item)
    {
        return false;
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
     * @param string     $name     The field name
     * @param string     $header   The header title
     * @param bool       $sort     Sortable column or not
     * @param string     $template The template
     * @param FieldAlias $alias    The alias
     *
     * @return AbstractAdminListConfigurator
     */
    public function addField($name, $header, $sort, $template = null, FieldAlias $alias = null)
    {
        $this->fields[] = new Field($name, $header, $sort, $template, $alias);

        return $this;
    }

    /**
     * @param string     $name     The field name
     * @param string     $header   The header title
     * @param string     $template The template
     * @param FieldAlias $alias    The alias
     *
     * @return AbstractAdminListConfigurator
     */
    public function addExportField($name, $header, $template = null, FieldAlias $alias = null)
    {
        $this->exportFields[] = new Field($name, $header, false, $template, $alias);

        return $this;
    }

    /**
     * @param string              $columnName The column name
     * @param FilterTypeInterface $type       The filter type
     * @param string              $filterName The name of the filter
     * @param array               $options    Options
     *
     * @return AbstractAdminListConfigurator
     */
    public function addFilter(
        $columnName,
        FilterTypeInterface $type = null,
        $filterName = null,
        array $options = []
    ) {
        $this->getFilterBuilder()->add($columnName, $type, $filterName, $options);

        return $this;
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
        $array = [];
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
        }

        return $this->exportFields;
    }

    /**
     * @param string   $label          The label, only used when the template equals null
     * @param callable $routeGenerator The generator used to generate the url of an item, when generating the item will
     *                                 be provided
     * @param string   $icon           The icon, only used when the template equals null
     * @param string   $template       The template, when not specified the label is shown
     *
     * @return AbstractAdminListConfigurator
     */
    public function addSimpleItemAction($label, $routeGenerator, $icon, $template = null)
    {
        return $this->addItemAction(new SimpleItemAction($routeGenerator, $icon, $label, $template));
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function addItemAction(ItemActionInterface $itemAction)
    {
        $this->itemActions[] = $itemAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasItemActions()
    {
        return !empty($this->itemActions);
    }

    /**
     * @return ItemActionInterface[]
     */
    public function getItemActions()
    {
        return $this->itemActions;
    }

    /**
     * @return AdminListConfiguratorInterface
     */
    public function addListAction(ListActionInterface $listAction)
    {
        $this->listActions[] = $listAction;

        return $this;
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
     * @return AdminListConfiguratorInterface
     */
    public function addBulkAction(BulkActionInterface $bulkAction)
    {
        $this->bulkActions[] = $bulkAction;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasBulkActions()
    {
        return !empty($this->bulkActions);
    }

    /**
     * @return BulkActionInterface[]
     */
    public function getBulkActions()
    {
        return $this->bulkActions;
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
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return mixed
     */
    public function getValue($item, $columnName)
    {
        if (\is_array($item)) {
            if (isset($item[$columnName])) {
                return $item[$columnName];
            }

            return '';
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        if ($accessor->isReadable($item, $columnName)) {
            $result = $accessor->getValue($item, $columnName);
        } else {
            return sprintf('undefined function [get/is/has]%s()', $columnName);
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
        if (\is_bool($result)) {
            return $result ? 'true' : 'false';
        }
        if ($result instanceof \DateTimeInterface) {
            return $result->format('Y-m-d H:i:s');
        }

        if ($result instanceof PersistentCollection) {
            $results = [];
            /* @var Object $entry */
            foreach ($result as $entry) {
                $results[] = $entry->getName();
            }
            if (empty($results)) {
                return '';
            }

            return implode(', ', $results);
        }

        if (is_array($result)) {
            return implode(', ', $result);
        }

        return $result;
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
    public function getViewTemplate()
    {
        return $this->viewTemplate;
    }

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setViewTemplate($template)
    {
        $this->viewTemplate = $template;

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
     * @return FilterBuilder
     */
    public function getFilterBuilder()
    {
        if (\is_null($this->filterBuilder)) {
            $this->filterBuilder = new FilterBuilder();
        }

        return $this->filterBuilder;
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function setFilterBuilder(FilterBuilder $filterBuilder)
    {
        $this->filterBuilder = $filterBuilder;

        return $this;
    }

    /**
     * Bind current request.
     */
    public function bindRequest(Request $request)
    {
        $query = $request->query;
        $session = $request->getSession();

        $adminListName = 'listconfig_' . $request->get('_route');

        $this->page = $request->query->getInt('page', 1);
        // Allow alphanumeric, _ & . in order by parameter!
        $this->orderBy = preg_replace('/[^[a-zA-Z0-9\_\.]]/', '', $request->query->get('orderBy', ''));
        $this->orderDirection = $request->query->getAlpha('orderDirection');

        // there is a session and the filter param is not set
        if ($session->has($adminListName) && !$query->has('filter')) {
            $adminListSessionData = $request->getSession()->get($adminListName);
            if (!$query->has('page')) {
                $this->page = $adminListSessionData['page'];
            }

            if (!$query->has('orderBy')) {
                $this->orderBy = $adminListSessionData['orderBy'];
            }

            if (!$query->has('orderDirection')) {
                $this->orderDirection = $adminListSessionData['orderDirection'];
            }
        }

        // save current parameters
        $session->set(
            $adminListName,
            [
                'page' => $this->page,
                'orderBy' => $this->orderBy,
                'orderDirection' => $this->orderDirection,
            ]
        );

        $this->getFilterBuilder()->bindRequest($request);
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
        $entityName = strtolower($this->getEntityName());
        $entityName = str_replace('\\', '_', $entityName);
        if (empty($suffix)) {
            return sprintf('%s_admin_%s', strtolower($this->getBundleName()), $entityName);
        }

        return sprintf('%s_admin_%s_%s', strtolower($this->getBundleName()), $entityName, $suffix);
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

    /**
     * Return extra parameters for use in list actions.
     *
     * @return array
     */
    public function getExtraParameters()
    {
        return [];
    }
}
