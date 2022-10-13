<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

interface PageTemplateConfigurationParserInterface
{
    /**
     * @return PageTemplateInterface
     */
    public function parse($name);
}
