<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

abstract class AbstractPagePartAdminConfigurator {

    abstract function getPossiblePagePartTypes();
    
    abstract function getName();
    
    abstract function getDefaultContext();

}
