<?php

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

class PagePartAdminFactory {

    public function createList(AbstractPagePartAdminConfigurator $configurator, $em, $page, $context, $container){
        return new PagePartAdmin($configurator, $em, $page, $context, $container);
    }
}
