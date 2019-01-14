<?php

namespace Kunstmaan\FixturesBundle\Parser\Spec;

use Kunstmaan\FixturesBundle\Loader\Fixture;

class Range implements SpecParserInterface
{
    const REGEX = '/{(\d)+\.\.(\d)+}$/';

    /**
     * Check if this parser is applicable
     *
     * @return bool
     */
    public function canParse($value)
    {
        return preg_match(self::REGEX, $value);
    }

    /**
     * Parse provided value into new data
     *
     * @param $spec
     * @param $fixture
     * @param $fixtures
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function parse(Fixture $fixture, array $fixtures, $spec)
    {
        preg_match(self::REGEX, $spec, $ranges);
        if (empty($ranges)) {
            return;
        }

        $range = $ranges[0];
        $name = substr($spec, 0, strpos($spec, $range));

        preg_match_all('/\d+/', $range, $keys);
        if (empty($keys)) {
            return;
        }

        $start = $keys[0][0];
        $end = $keys[0][1];

        if ($start > $end) {
            throw new \Exception('Range start can not be biggen than range end for fixture ' . $spec);
        }

        for ($i = $start; $i <= $end; ++$i) {
            $newFixture = clone $fixture;
            $newFixture->setName($name . $i);
            $newFixture->setSpec($i);
            $fixtures[$name . $i] = $newFixture;
        }

        return $fixtures;
    }
}
