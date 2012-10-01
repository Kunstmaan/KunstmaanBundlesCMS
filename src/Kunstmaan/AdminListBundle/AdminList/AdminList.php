<?php

namespace Kunstmaan\AdminListBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AdminListConfiguratorInterface;

use Symfony\Component\HttpFoundation\Request;

use Pagerfanta\Pagerfanta;

/**
 * AdminList
 */
class AdminList
{

    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var AdminListConfiguratorInterface
     */
    protected $configurator = null;

    /**
     * @param AdminListConfiguratorInterface $configurator The configurator
     */
    public function __construct(AdminListConfiguratorInterface $configurator)
    {
        $this->configurator = $configurator;
        $this->configurator->buildFilters();
        $this->configurator->buildFields();
        $this->configurator->buildActions();
    }

    /**
     * @return AdminListConfiguratorInterface|null
     */
    public function getConfigurator()
    {
        return $this->configurator;
    }

    /**
     * @return FilterBuilder
     */
    public function getAdminListFilter()
    {
        return $this->configurator->getFilterBuilder();
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        $this->configurator->bindRequest($request);
    }

    /**
     * @return Field[]
     */
    public function getColumns()
    {
        return $this->configurator->getFields();
    }

    /**
     * @return Field[]
     */
    public function getExportColumns()
    {
        return $this->configurator->getExportFields();
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->configurator->getCount();
    }

    /**
     * @return array|null
     */
    public function getItems()
    {
        return $this->configurator->getItems();
    }

    /**
     * @param string $columnName
     *
     * @return bool
     */
    public function hasSort($columnName)
    {
        return in_array($columnName, $this->configurator->getSortFields());
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canEdit($item)
    {
        return $this->configurator->canEdit($item);
    }

    /**
     * @return bool
     */
    public function canAdd()
    {
        return $this->configurator->canAdd();
    }

    /**
     * @return array
     */
    public function getIndexUrlFor()
    {
        return $this->configurator->getIndexUrlFor();
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return $this->configurator->getEditUrlFor($item);
    }

    /**
     * @param mixed $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return $this->configurator->getDeleteUrlFor($item);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function getAddUrlFor(array $params)
    {
        return $this->configurator->getAddUrlFor($params);
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function canDelete($item)
    {
        return $this->configurator->canDelete($item);
    }

    /**
     * @return bool
     */
    public function canExport()
    {
        return $this->configurator->canExport();
    }

    /**
     * @return string
     */
    public function getExportUrlFor()
    {
        return $this->configurator->getExportUrlFor();
    }

    /**
     * @param object|array $object    The object
     * @param string       $attribute The attribute
     *
     * @return mixed
     */
    public function getValue($object, $attribute)
    {
        return $this->configurator->getValue($object, $attribute);
    }

    /**
     * @param object|array $object    The object
     * @param string       $attribute The attribute
     *
     * @return string
     */
    public function getStringValue($object, $attribute)
    {
        return $this->configurator->getStringValue($object, $attribute);
    }

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->configurator->getOrderBy();
    }

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->configurator->getOrderDirection();
    }

    /**
     * @return array
     */
    public function getCustomActions()
    {
        return $this->configurator->getCustomActions();
    }

    /**
     * @return bool
     */
    public function hasCustomActions()
    {
        return $this->configurator->hasCustomActions();
    }

    /**
     * @return bool
     */
    public function hasListActions()
    {
        return $this->configurator->hasListActions();
    }

    /**
     * @return array
     */
    public function getListActions()
    {
        return $this->configurator->getListActions();
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        return $this->configurator->getPagerfanta();
    }
}
