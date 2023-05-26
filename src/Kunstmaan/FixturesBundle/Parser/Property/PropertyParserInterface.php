<?php

namespace Kunstmaan\FixturesBundle\Parser\Property;

interface PropertyParserInterface
{
    /**
     * Check if this parser is applicable
     *
     * @return bool
     */
    public function canParse($value);

    /**
     * Parse provided value into new data
     */
    public function parse($value, $providers, $references);
}
