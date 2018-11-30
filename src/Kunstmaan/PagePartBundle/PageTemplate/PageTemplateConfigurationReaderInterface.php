<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

interface PageTemplateConfigurationReaderInterface
{
    /**
     * @param HasPageTemplateInterface $page
     *
     * @throws \Exception
     *
     * @return PageTemplateInterface[]
     */
    public function getPageTemplates(HasPageTemplateInterface $page);
}
