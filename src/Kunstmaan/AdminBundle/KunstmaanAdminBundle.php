<?php

namespace Kunstmaan\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KunstmaanAdminBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
