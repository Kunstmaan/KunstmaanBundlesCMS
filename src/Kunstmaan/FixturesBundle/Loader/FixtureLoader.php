<?php

namespace Kunstmaan\FixturesBundle\Loader;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;

abstract class FixtureLoader implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $options = $this->getOptions();

        $parser = new Parser();

        $data = [];
        foreach ($this->getFixtures() as $fixture) {
            $data[] = $parser->parse(file_get_contents($fixture));
        }

        $fixtures = $this->initFixtures($data);
        $builder = $this->container->get('kunstmaan_fixtures.builder.builder');
        $locale = isset($options['locale']) ? $options['locale'] : 'en_US';

        foreach ($this->getProviders() as $provider) {
            $builder->addProvider($provider);
        }

        /*
         * because of faker's magic calls we'll want to add this as last provider
         */
        $builder->addProvider(Factory::create($locale));
        $builder->setFixtures($fixtures);
        $builder->buildFixtures($manager);
    }

    /**
     * Parse specs and initiate fixtures
     *
     * @param $data
     *
     * @return array|mixed
     */
    private function initFixtures($data)
    {
        $fixtures = [];
        $parser = $this->container->get('kunstmaan_fixtures.parser.parser');

        foreach ($data as $file) {
            foreach ($file as $class => $specs) {
                foreach ($specs as $name => $options) {
                    $fixture = new Fixture($name, $class, $options);
                    $fixtures = $parser->parseSpec($name, $fixture, $fixtures);
                }
            }
        }

        return $fixtures;
    }

    abstract protected function getFixtures();

    public function getOptions()
    {
        return ['locale' => 'en_US'];
    }

    public function getProviders()
    {
        return [$this];
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
