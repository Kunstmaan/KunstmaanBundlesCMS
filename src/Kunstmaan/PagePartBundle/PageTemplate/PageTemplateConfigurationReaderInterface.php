<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

interface PageTemplateConfigurationReaderInterface
{
    /**
     * @return PageTemplateInterface[]
     *
     * @throws \Exception
     */
    public function getPageTemplates(HasPageTemplateInterface $page);
}
