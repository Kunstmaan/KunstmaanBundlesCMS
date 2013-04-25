<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

/**
 * An interface for something that contains pageparts
 */
interface HasPagePartsInterface
{

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations();

}
