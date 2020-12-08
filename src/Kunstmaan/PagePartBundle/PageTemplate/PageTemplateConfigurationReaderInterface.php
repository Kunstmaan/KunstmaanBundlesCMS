<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

interface PageTemplateConfigurationReaderInterface
{
    /**
     * @throws \Exception
     *
     * @return PageTemplateInterface[]
     */
    public function getPageTemplates(HasPageTemplateInterface $page);
}
