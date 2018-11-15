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
     *
     * @param $value
     * @param $providers
     * @param $references
     *
     * @return mixed
     */
    public function parse($value, $providers, $references);
}
