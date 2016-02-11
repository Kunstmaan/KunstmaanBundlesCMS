<?php
namespace Kunstmaan\AdminListBundle\Service\Pager;

use Pagerfanta\View\Template\TwitterBootstrapTemplate;

class TwitterBootstrapAdminListTemplate extends TwitterBootstrapTemplate
{
    protected function generateRoute($page)
    {

        return call_user_func(function($page) {
            return '?page='.$page;
        }, $page);
    }
}
