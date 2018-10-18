<?php

namespace Kunstmaan\FormBundle\Tests\Entity;

use Kunstmaan\NodeBundle\Entity\AbstractPage;

class FakePage extends AbstractPage
{
    public function getPossibleChildTypes()
    {
        return [];
    }
}
