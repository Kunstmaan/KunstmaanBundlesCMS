<?php

namespace  Kunstmaan\PagePartBundle\Helper;

use Kunstmaan\PagePartBundle\PageTemplate\PageTemplateInterface;

/**
 * HasPageTemplateInterface
 */
interface HasPageTemplateInterface extends HasPagePartsInterface
{
    /**
     * @return PageTemplateInterface[]|string[]
     */
    public function getPageTemplates();
}
