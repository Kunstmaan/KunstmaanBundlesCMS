<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use InvalidArgumentException;
use Kunstmaan\AdminListBundle\AdminList\BulkAction\BulkActionInterface;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;
use Kunstmaan\AdminListBundle\AdminList\ListAction\ListActionInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

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
     * Configure the fields you can filter on
     */
    public function buildFilters();

    /**
     * Configure the actions for each item
     */
    public function buildItemActions();

    /**
     * Configure the actions that can be executed on the whole list
     */
    public function buildListActions();

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
    public function getIndexUrl();

    /**
     * Get the url to export the listed items
     *
     * @return array
     */
    public function getExportUrl();

    /**
     * @param object $entity
     *
     * @throws InvalidArgumentException
     *
     * @return string FQCN of form type
     */
    public function getAdminType($entity);

    /**
     * @param object|array $item
     *
     * @return bool
     */
    public function canEdit($item);

    public function canView($item);

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
     * @return bool
     */
    public function hasItemActions();

    /**
     * @return ItemActionInterface[]
     */
    public function getItemActions();

    /**
     * @return bool
     */
    public function hasListActions();

    /**
     * @return ListActionInterface[]
     */
    public function getListActions();

    /**
     * @return bool
     */
    public function hasBulkActions();

    /**
     * @return BulkActionInterface[]
     */
    public function getBulkActions();

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
     * @return string
     */
    public function getListTemplate();

    /**
     * @return string
     */
    public function getAddTemplate();

    /**
     * @return string
     */
    public function getEditTemplate();

    /**
     * @return string
     */
    public function getDeleteTemplate();

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
     * @return FilterBuilder
     */
    public function getFilterBuilder();

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

    /**
     * Return extra parameters for use in list actions.
     *
     * @return array
     */
    public function getExtraParameters();
}
