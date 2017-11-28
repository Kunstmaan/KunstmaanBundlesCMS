<?php

namespace  Kunstmaan\AdminBundle\Traits\DependencyInjection;

use Kunstmaan\AdminListBundle\AdminList\AdminListFactory;

/**
 * Trait AdminListFactoryTrait
 */
trait AdminListFactoryTrait
{
    /**
     * @var AdminListFactory
     */
    protected $adminListFactory;

    /**
     * @return AdminListFactory
     */
    public function getAdminListFactory()
    {
        if (null !== $this->container && null === $this->adminListFactory) {
            $this->adminListFactory = $this->container->get("kunstmaan_adminlist.factory");
        }

        return $this->adminListFactory;
    }

    /**
     * @required
     * @param AdminListFactory $adminListFactory
     */
    public function setAdminListFactory(AdminListFactory $adminListFactory = null)
    {
        $this->adminListFactory = $adminListFactory;
    }
}
