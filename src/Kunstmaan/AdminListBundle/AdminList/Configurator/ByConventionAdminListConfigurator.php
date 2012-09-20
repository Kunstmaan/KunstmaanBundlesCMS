<?php

namespace Kunstmaan\AdminListBundle\AdminList\Configurator;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;

/**
 * Generates adminlist paths by convention because this is a repeating effort.
 *
 * To use this, the routing names of the adminlist methods in your controller must follow this format:
 * <FullBundleName>_admin_<LowerCaseEntityNamePlural>[_<add/edit/delete/export>]
 *
 * Additionally, the delete action in your controller must be named 'deleteAction'.
 * If your controller is not located in a subfolder of the bundle's Controller folder, you must provide it's path at construction.
 * eg. /src/FullBundleName/Controller/Subfolder/EntityController
 * -> $controllerPath = 'Subfolder\\Entity'
 */
abstract class ByConventionAdminListConfigurator extends AbstractAdminListConfigurator
{
    const SUFFIX_ADD = 'add';
    const SUFFIX_EDIT = 'edit';
    const SUFFIX_EXPORT = 'export';
    const SUFFIX_DELETE = 'delete';

    protected $bundleName;
    protected $entityName;
    protected $controllerPath;

    /**
     * @param string $bundleName     The bundle name
     * @param string $entityName     The class name of the entity (not the full classname)
     * @param string $controllerPath The path of the controller
     */
    public function __construct($bundleName, $entityName, $controllerPath = null)
    {
        $this->bundleName = $bundleName;
        $this->entityName = $entityName;
        $this->controllerPath = $controllerPath;
    }

    /**
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
     * @param object $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return array(
            'path'		=> $this->getPathByConvention($this::SUFFIX_EDIT),
            'params'	=> array('entity_id' => $item->getId()
            ));
    }

    /**
     * @param object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return array(
            'action' => sprintf('%s:delete', $this->getControllerPath()),
            'path' => $this->getPathByConvention($this::SUFFIX_DELETE)
        );
    }

    /**
     * @return string
     */
    public function getIndexUrlFor()
    {
        return $this->getPathByConvention();
    }

    /**
     * @return string
     */
    public function getRepositoryName()
    {
        return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
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
     * @return string
     */
    public function getControllerPathByConvention()
    {
        return sprintf('%s:%s', $this->getBundleName(), $this->getEntityName());
    }

    /**
     * @return string
     */
    public function getBundleName()
    {
        return $this->bundleName;
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @return string
     */
    public function getControllerPath()
    {
        if (!empty($this->controllerPath)) {
            return sprintf('%s:%s', $this->getBundleName(), $this->controllerPath);
        }

        return $this->getControllerPathByConvention();
    }
}
