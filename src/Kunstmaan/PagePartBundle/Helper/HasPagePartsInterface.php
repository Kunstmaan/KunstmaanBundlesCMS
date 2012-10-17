<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

interface HasPagePartsInterface
{

    /**
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurations();

}
