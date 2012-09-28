<?php
namespace Kunstmaan\AdminListBundle\AdminList;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;

use Pagerfanta\Pagerfanta;

/**
 * Implement this interface to create your own admin list
 */
interface AdminListConfiguratorInterface
{
    /**
     * Configure the visible columns
     */
    public function buildFields();

    /**
     * Return the url to edit the given $item
     *
     * @param object|array $item
     *
     * @return array
     */
    public function getEditUrlFor($item);

    /**
     * Configure the types of items you can add
     *
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params = array());

    /**
     * Get the delete url for the given $item
     *
     * @param object|array $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item);

    /**
     * Return the url to list all the items
     *
     * @return array
     */
    public function getIndexUrlFor();

    /**
     * @param object $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return AbstractType
     */
    public function getAdminType($entity);

    /**
     * @param AbstractType $type
     *
     * @return AdminListConfiguratorInterface
     */
    public function setAdminType(AbstractType $type);

    /**
     * Configure the fields you can filter on
     */
    public function buildFilters();

    /**
     * configure the actions for each line
     */
    public function buildActions();

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item);

    /**
     * @param string $name     The field name
     * @param string $header   The header title
     * @param string $sort     Sortable column or not
     * @param string $template The template
     *
     * @return AdminListConfiguratorInterface
     */
    public function addField($name, $header, $sort, $template = null);

    /**
     * @param string $name     The field name
     * @param string $header   The header title
     * @param string $template The template
     *
     * @return AdminListConfiguratorInterface
     */
    public function addExportField($name, $header, $template = null);

    /**
     * Configure if it's possible to delete the given $item
     *
     * @param object|array $item
     *
     * @return bool
     */
    public function canDelete($item);

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canAdd();

    /**
     * Configure if it's possible to add new items
     *
     * @return bool
     */
    public function canExport();

    /**
     * Get the url to export the listed items
     *
     * @return string
     */
    public function getExportUrlFor();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return array
     */
    public function getSortFields();

    /**
     * @return Field[]
     */
    public function getFields();

    /**
     * @return Field[]
     */
    public function getExportFields();

    /**
     * @param string $label    The label, only used when the template equals null
     * @param string $url      The action url
     * @param string $icon     The icon, only used when the template equals null
     * @param string $template The template, when not specified the label is shown
     *
     * @return AdminListConfiguratorInterface
     */
    public function addSimpleAction($label, $url, $icon, $template = null);

    /**
     * @param ListActionInterface $customAction
     *
     * @return AdminListConfiguratorInterface
     */
    public function addCustomAction(ListActionInterface $customAction);

    /**
     * @return bool
     */
    public function hasCustomActions();

    /**
     * @return ListActionInterface[]
     */
    public function getCustomActions();

    /**
     * @return bool
     */
    public function hasListActions();

    /**
     * @return ListActionInterface[]
     */
    public function getListActions();

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return mixed
     */
    public function getValue($item, $columnName);

    /**
     * @param array|object $item       The item
     * @param string       $columnName The column name
     *
     * @return string
     */
    public function getStringValue($item, $columnName);

    /**
     * @param ListActionInterface $listAction
     *
     * @return AdminListConfiguratorInterface
     */
    public function addListAction(ListActionInterface $listAction);

    /**
     * @return string
     */
    public function getListTemplate();

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setListTemplate($template);

    /**
     * @return string
     */
    public function getAddTemplate();

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setAddTemplate($template);

    /**
     * @return string
     */
    public function getEditTemplate();

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setEditTemplate($template);

    /**
     * @return string
     */
    public function getDeleteTemplate();

    /**
     * @param string $template
     *
     * @return AdminListConfiguratorInterface
     */
    public function setDeleteTemplate($template);

    /**
     * You can override this method to do some custom things you need to do when adding an entity
     *
     * @param object $entity
     *
     * @return mixed
     */
    public function decorateNewEntity($entity);

    /**
     * Return total number of items.
     *
     * @return int
     */
    public function getCount();

    /**
     * Return items on current page.
     *
     * @return mixed
     */
    public function getItems();

    /**
     * Bind request
     *
     * @param Request $request
     */
    public function bindRequest(Request $request);

    /**
     * Get current pagerfanta
     *
     * @return Pagerfanta
     */
    public function getPagerfanta();

    /**
     * @return AdminListFilter
     */
    public function getAdminListFilter();

    /**
     * Return current sorting column.
     *
     * @return string
     */
    public function getOrderBy();

    /**
     * Return current sorting direction.
     *
     * @return string
     */
    public function getOrderDirection();
}
