<?php

namespace Kunstmaan\PagePartBundle\PagePartConfigurationReader;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

interface PagePartConfigurationReaderInterface
{
    /**
     * @return AbstractPagePartAdminConfigurator[]
     *
     * @throws \Exception
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page);

    /**
     * @return string[]
     *
     * @throws \Exception
     */
    public function getPagePartContexts(HasPagePartsInterface $page);
}
