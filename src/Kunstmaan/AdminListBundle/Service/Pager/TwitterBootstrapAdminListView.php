<?php
namespace Kunstmaan\AdminListBundle\Service\Pager;

use Pagerfanta\View\DefaultView;

class TwitterBootstrapAdminListView extends DefaultView
{
    protected function createDefaultTemplate()
    {
        return new TwitterBootstrapAdminListTemplate();
    }

    protected function getDefaultProximity()
    {
        return 3;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'twitter_bootstrap_admin_list';
    }
}
