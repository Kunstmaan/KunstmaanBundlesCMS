<?php

namespace Kunstmaan\UserManagementBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;

/**
 * Abstract admin list configurator used by the Group, Log, Role and User configurators
 */
abstract class AbstractSettingsAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * Override path convention (because settings is a virtual admin subtree)
     *
     * @param string $suffix
     *
     * @return string
     */
    public function getPathByConvention($suffix = null)
    {
        if (empty($suffix)) {
            return sprintf('KunstmaanUserManagementBundle_settings_%ss', strtolower($this->getEntityName()));
        }

        return sprintf('KunstmaanUserManagementBundle_settings_%ss_%s', strtolower($this->getEntityName()), $suffix);
    }

    public function getAdminType($item)
    {
        return null;
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'KunstmaanAdminBundle';
    }

    /**
     * Override controller path (because actions for different entities are defined in a single Settings controller)
     *
     * @return string
     */
    public function getControllerPath()
    {
        return 'KunstmaanUserManagementBundle:Settings';
    }
}
