<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;

/**
 * An interface for something that contains pageparts
 */
interface HasPagePartsInterface
{
    public function getId();

    /**
     * @return PagePartAdminConfiguratorInterface[]
     */
    public function getPagePartAdminConfigurations();
}
