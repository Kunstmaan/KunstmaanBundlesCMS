<?php

namespace Kunstmaan\UserManagementBundle\AdminList;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminListBundle\Utils\EntityDetails;

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
        $entityPart = EntityDetails::getEntityPart($this->getEntityClass());

        $entityName = strtolower($entityPart);
        $entityName = str_replace('\\', '_', $entityName);
        if (null === $suffix || $suffix === '') {
            return sprintf('KunstmaanUserManagementBundle_settings_%ss', $entityName);
        }

        return sprintf('KunstmaanUserManagementBundle_settings_%ss_%s', $entityName, $suffix);
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
