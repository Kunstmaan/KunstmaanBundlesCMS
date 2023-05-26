<?php

namespace Kunstmaan\FixturesBundle\Parser\Spec;

use Kunstmaan\FixturesBundle\Loader\Fixture;

interface SpecParserInterface
{
    /**
     * Check if this parser is applicable
     *
     * @return bool
     */
    public function canParse($value);

    public function parse(Fixture $fixture, array $fixtures, $spec);
}
