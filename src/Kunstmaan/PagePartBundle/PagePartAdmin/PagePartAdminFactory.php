<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kris
 * Date: 15/11/11
 * Time: 23:02
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\PagePartBundle\PagePartAdmin;

class PagePartAdminFactory {

    public function createList(AbstractPagePartAdminConfigurator $configurator, $em, $page, $context = "main", $container){
        return new PagePartAdmin($configurator, $em, $page, $context, $container);
    }
}
