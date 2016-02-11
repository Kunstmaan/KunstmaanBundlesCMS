<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplate;

/**
 * HasPageTemplateInterface
 */
interface HasPageTemplateInterface extends HasPagePartsInterface
{

    /**
     * @return PageTemplate[]
     */
    public function getPageTemplates();

}
