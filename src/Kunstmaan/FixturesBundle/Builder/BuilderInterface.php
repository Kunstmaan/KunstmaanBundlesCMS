<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Kunstmaan\FixturesBundle\Loader\Fixture;

interface BuilderInterface
{
    public function canBuild(Fixture $fixture);

    public function preBuild(Fixture $fixture);

    public function postBuild(Fixture $fixture);

    public function postFlushBuild(Fixture $fixture);
}
