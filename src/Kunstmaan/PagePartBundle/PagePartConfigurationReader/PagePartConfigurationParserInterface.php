<?php

namespace Kunstmaan\PagePartBundle\PagePartConfigurationReader;

use Kunstmaan\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;

interface PagePartConfigurationParserInterface
{

    /**
     * This will read the $name file and parse it to the PageTemplate
     *
     * @param string $name
     * @param PagePartAdminConfiguratorInterface[] $existing
     *
     * @return PagePartAdminConfiguratorInterface
     * @throws \Exception
     */
    public function parse($name, array $existing = []);
}
