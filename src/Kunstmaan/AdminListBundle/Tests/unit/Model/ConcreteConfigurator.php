<?php

namespace Kunstmaan\AdminListBundle\Tests\unit\Model;

use ArrayIterator;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Pagerfanta\Pagerfanta;

class ConcreteConfigurator extends AbstractAdminListConfigurator
{
    /**
     * @return string
     */
    public function getBundleName()
    {
        return 'xyz';
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        return 'Xyz';
    }

    /**
     * @return mixed
     */
    public function buildFields()
    {
        return true;
    }

    /**
     * @param array|object $item
     *
     * @return array
     */
    public function getEditUrlFor($item)
    {
        return [
            'Xyz' => [
                'path' => 'xyz_admin_xyz_edit',
                'params' => [],
            ],
        ];
    }

    /**
     * @param array|object $item
     *
     * @return array
     */
    public function getDeleteUrlFor($item)
    {
        return [
            'Xyz' => [
                'path' => 'xyz_admin_xyz_delete',
                'params' => [],
            ],
        ];
    }

    /**
     * @return int
     */
    public function getCount()
    {
        // TODO: Implement getCount() method.
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        return [
            'some' => 'item',
        ];
    }

    /**
     * @return Pagerfanta
     */
    public function getPagerfanta()
    {
        return 'fake pagerfanta';
    }

    /**
     * @return mixed
     */
    public function getIterator()
    {
        return new ArrayIterator();
    }
}
