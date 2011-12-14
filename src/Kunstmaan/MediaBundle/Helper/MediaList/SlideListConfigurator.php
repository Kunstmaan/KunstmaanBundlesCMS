<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kristof
 * Date: 15/11/11
 * Time: 22:23
 * To change this template use File | Settings | File Templates.
 */

namespace Kunstmaan\MediaBundle\Helper\MediaList;

use Kunstmaan\AdminListBundle\AdminList\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\AdminList\AdminListFilter;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\StringFilterType;
use Kunstmaan\AdminListBundle\AdminList\FilterDefinitions\BooleanFilterType;

class SlideListConfigurator extends AbstractAdminListConfigurator{

    public function buildFilters(AdminListFilter $builder){
        $builder->add('name', new StringFilterType("name"));
        $builder->add('type', new StringFilterType("type"));
    }

    public function getSortFields() {
        $array = array();
        $array[] = "name";
        $array[] = "type";
        $array[] = "createdAt";
        $array[] = "updatedAt";
        return $array;
    }

    public function configureListFields(&$array)
    {
        $array[] = "name";
        $array[] = "type";
        $array[] = "createdAt";
        $array[] = "updatedAt";
    }

    public function canEdit($item) {
        return true;
    }

    public function getEditUrlFor($item) {
        return "/app_dev.php/admin/media/".$item->getId();
    }

    public function canDelete($item) {
        return true;
    }

    public function getRepositoryName(){
        return 'KunstmaanMediaBundle:Slide';
    }

    function adaptQueryBuilder($querybuilder){
        parent::adaptQueryBuilder($querybuilder);
    }
}
