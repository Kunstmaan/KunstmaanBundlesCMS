<?php

namespace Kunstmaan\FixturesBundle\Provider;

use Kunstmaan\FixturesBundle\Loader\Fixture;

class Spec
{
    public function current(Fixture $fixture)
    {
        return $fixture->getSpec();
    }
}
