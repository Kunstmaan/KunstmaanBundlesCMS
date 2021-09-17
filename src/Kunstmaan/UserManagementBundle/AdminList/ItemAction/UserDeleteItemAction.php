<?php

namespace Kunstmaan\UserManagementBundle\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\ItemActionInterface;

class UserDeleteItemAction implements ItemActionInterface
{
    public function getUrlFor($item)
    {
        return null;
    }

    public function getLabelFor($item)
    {
        return 'Delete';
    }

    public function getIconFor($item)
    {
        return 'trash-alt';
    }

    public function getTemplate()
    {
        return '@KunstmaanUserManagement/AdminList/ItemAction/delete.html.twig';
    }
}
