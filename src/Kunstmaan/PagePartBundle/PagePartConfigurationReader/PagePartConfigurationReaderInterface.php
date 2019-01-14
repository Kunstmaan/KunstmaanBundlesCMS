<?php

namespace Kunstmaan\PagePartBundle\PagePartConfigurationReader;

use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

interface PagePartConfigurationReaderInterface
{
    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     *
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page);

    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     *
     * @return string[]
     */
    public function getPagePartContexts(HasPagePartsInterface $page);
}
