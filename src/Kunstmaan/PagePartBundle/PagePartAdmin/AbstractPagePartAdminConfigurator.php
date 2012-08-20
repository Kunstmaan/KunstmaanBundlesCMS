<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;
abstract class AbstractPagePartAdminConfigurator
{

    /**
     * @return array
     */
    abstract function getPossiblePagePartTypes();

    /**
     * @return string
     */
    abstract function getName();

    /**
     * @return string
     */
    abstract function getDefaultContext();
}
