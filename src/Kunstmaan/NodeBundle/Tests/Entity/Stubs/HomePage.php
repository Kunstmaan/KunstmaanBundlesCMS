<?php

namespace Kunstmaan\NodeBundle\Tests\Entity\Stubs;

use Kunstmaan\NodeBundle\Entity\AbstractPage;

class HomePage extends AbstractPage
{
    public function getDefaultAdminType(): string
    {
        return 'admin_type';
    }

    public function getPossibleChildTypes(): array
    {
        return [];
    }
}
