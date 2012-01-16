<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 22:32
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

abstract class AbstractPagePartAdminConfigurator {

    abstract function getPossiblePagePartTypes();
    
    abstract function getName();
    
    abstract function getDefaultContext();

}
