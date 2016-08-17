<?php

namespace Kunstmaan\FixturesBundle\Parser;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\FixturesBundle\Parser\Property\PropertyParserInterface;
use Kunstmaan\FixturesBundle\Parser\Spec\SpecParserInterface;

class Parser
{
    /**
     * @var PropertyParserInterface[]
     */
    private $parsers;

    /**
     * @var SpecParserInterface[]
     */
    private $specParsers;

    public function __construct()
    {
        $this->parsers = new ArrayCollection();
        $this->specParsers = new ArrayCollection();
    }

    public function parseFixture(Fixture $fixture, $providers, $fixtures = [])
    {
        $entities = [];
        foreach ($fixtures as $key => $ref) {
            $entities[$key] = $ref->getEntity();
        }

        $fixture->setProperties($this->parseArray($fixture->getProperties(), $providers, $entities, [
            $fixture,
            'fixtures' => $fixtures,
        ]));

        $fixture->setParameters($this->parseArray($fixture->getParameters(), $providers, $fixtures, [
            $fixture,
            'fixtures' => $fixtures,
        ]));

        $fixture->setTranslations($this->parseArray($fixture->getTranslations(), $providers, $fixtures, [
            $fixture,
            'fixtures' => $fixtures,
        ]));
    }

    public function parseEntity($entity, $providers, $fixtures = [], $additional = [])
    {
        $refl = new \ReflectionClass($entity);
        $properties = $refl->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($entity);

            foreach ($this->parsers as $parser) {
                if ($parser->canParse($value)) {
                    $value = $parser->parse($value, $providers, $fixtures, $additional);
                    $property->setValue($entity, $value);
                }
            }
        }
    }

    public function parseArray($array, $providers, $fixtures = [], $additional = [])
    {
        if (empty($array)) {
            return $array;
        }

        foreach ($array as $key => $item) {
            if (is_array($item)) {
                $array[$key] = $this->parseArray($item, $providers, $fixtures);
            } else {
                $array[$key] = $this->parse($item, $providers, $fixtures, $additional);
            }
        }

        return $array;
    }

    public function parse($value, $providers, $fixtures = [], $additional = [])
    {
        foreach ($this->parsers as $parser) {
            if ($parser->canParse($value)) {
                $value = $parser->parse($value, $providers, $fixtures, $additional);
            }
        }

        return $value;
    }

    public function parseSpec($value, $fixture, $fixtures = [])
    {
        foreach ($this->specParsers as $parser) {
            if ($parser->canParse($value)) {
                return $parser->parse($fixture, $fixtures, $value);
            }
        }

        $fixtures[$value] = $fixture;
        return $fixtures;
    }

    public function addParser(PropertyParserInterface $parser, $alias)
    {
        $this->parsers->set($alias, $parser);

        return $this;
    }

    public function addSpecParser(SpecParserInterface $parser, $alias)
    {
        $this->specParsers->set($alias, $parser);

        return $this;
    }
}
