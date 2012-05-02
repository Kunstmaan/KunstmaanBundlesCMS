<?php

namespace Kunstmaan\AdminListBundle\AdminList;

class AdminListFactory {

    public function createList(AbstractAdminListConfigurator $configurator, $em, $queryparams = array()){
        return new AdminList($configurator, $em, $queryparams);
    }
}
