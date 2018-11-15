<?php

namespace Kunstmaan\FixturesBundle\Parser\Spec;

use Kunstmaan\FixturesBundle\Loader\Fixture;

class Listed implements SpecParserInterface
{
    const REGEX = '/{([^,]+,?)+}$/';

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
        preg_match(self::REGEX, $spec, $matches);
        if (empty($matches)) {
            return $fixtures;
        }

        $list = $matches[0];
        $name = substr($spec, 0, strpos($spec, $list));

        preg_match_all('/[^,{}]+/', $list, $keys);
        if (empty($keys)) {
            return $fixtures;
        }

        $keys = array_map(function ($key) {
            return trim($key);
        }, $keys[0]);
        foreach ($keys as $item) {
            $newFixture = clone $fixture;
            $newFixture->setName($name . $item);
            $newFixture->setSpec($item);
            $fixtures[$name . $item] = $newFixture;
        }

        return $fixtures;
    }
}
