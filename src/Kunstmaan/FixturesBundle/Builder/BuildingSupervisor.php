<?php

namespace Kunstmaan\FixturesBundle\Builder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\FixturesBundle\Loader\Fixture;
use Kunstmaan\FixturesBundle\Parser\Parser;
use Kunstmaan\FixturesBundle\Populator\Populator;

class BuildingSupervisor
{
    /**
     * @var Fixture[]
     */
    private $fixtures;

    /**
     * @var BuilderInterface[]
     */
    private $builders;

    /**
     * @var Parser
     */
    private $parser;

    private $providers;

    /**
     * @var Populator
     */
    private $populater;

    public function __construct(Parser $parser, Populator $populator)
    {
        $this->fixtures = [];
        $this->builders = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->parser = $parser;
        $this->populater = $populator;
    }

    public function buildFixtures(ObjectManager $manager)
    {
        foreach ($this->fixtures as $fixture) {
            $classname = $fixture->getClass();
            $entity = new $classname();
            $fixture->setEntity($entity);
            $this->parser->parseFixture($fixture, $this->providers, $this->fixtures);

            foreach ($this->builders as $builder) {
                if ($builder->canBuild($fixture)) {
                    $builder->preBuild($fixture);
                }
            }

            $this->populater->populate($fixture->getEntity(), $fixture->getProperties());
            $manager->persist($entity);

            foreach ($this->builders as $builder) {
                if ($builder->canBuild($fixture)) {
                    $builder->postBuild($fixture);
                }
            }
        }
        $manager->flush();

        foreach ($this->fixtures as $fixture) {
            foreach ($this->builders as $builder) {
                if ($builder->canBuild($fixture)) {
                    $builder->postFlushBuild($fixture);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getFixtures()
    {
        return $this->fixtures;
    }

    /**
     * @param mixed $fixtures
     *
     * @return BuildingSupervisor
     */
    public function setFixtures(array $fixtures)
    {
        $this->fixtures = $fixtures;

        return $this;
    }

    public function addBuilder(BuilderInterface $builder, $alias)
    {
        $this->builders->set($alias, $builder);

        return $this;
    }

    public function addProvider($provider)
    {
        $this->providers->add($provider);

        return $this;
    }
}
