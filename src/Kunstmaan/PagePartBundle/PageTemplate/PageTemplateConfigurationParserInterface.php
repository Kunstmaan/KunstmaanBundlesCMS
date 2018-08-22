<?php

namespace Kunstmaan\PagePartBundle\PageTemplate;

interface PageTemplateConfigurationParserInterface
{
    /**
     * @param $name
     *
     * @return PageTemplateInterface
     */
    public function parse($name);
}
