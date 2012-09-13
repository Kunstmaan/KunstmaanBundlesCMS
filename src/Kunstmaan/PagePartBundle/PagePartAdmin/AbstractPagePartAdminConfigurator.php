<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;
abstract class AbstractPagePartAdminConfigurator
{

    /**
     * @return array
     */
    abstract public function getPossiblePagePartTypes();

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * @return string
     */
    abstract public function getDefaultContext();
}
