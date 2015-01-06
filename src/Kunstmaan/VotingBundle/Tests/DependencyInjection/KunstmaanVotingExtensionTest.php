<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Kunstmaan\VotingBundle\DependencyInjection\KunstmaanVotingExtension;

class KunstmaanVotingExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var KunstmaanVotingExtension
     */
    private $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    private $root;

    public function setUp()
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root      = "kuma_voting";
    }

    public function testGetConfigWithOverrideDefaultValue()
    {
        $container = $this->getContainer();
        $container->setParameter('voting_default_value', 2);
        $this->extension->load(array(array()), $container);
        $this->assertTrue($container->hasParameter($this->root . ".actions"));
        $this->assertTrue(is_array($container->getParameter($this->root . ".actions")));

        $actions = $container->getParameter($this->root . ".actions");
        if (isset($actions['up_vote'])) {
            $this->assertEquals(2, $actions['up_vote']['default_value']);
        }
        if (isset($actions['down_vote'])) {
            $this->assertEquals(-2, $actions['down_vote']['default_value']);
        }
        if (isset($actions['facebook_like'])) {
            $this->assertEquals(2, $actions['facebook_like']['default_value']);
        }
    }

    public function testGetConfigWithDefaultValues()
    {
        $container = $this->getContainer();
        $this->extension->load(array(array()), $container);
        $this->assertTrue($container->hasParameter($this->root . ".actions"));
        $this->assertTrue(is_array($container->getParameter($this->root . ".actions")));

        $actions = $container->getParameter($this->root . ".actions");
        if (isset($actions['up_vote'])) {
            $this->assertEquals(1, $actions['up_vote']['default_value']);
        }
        if (isset($actions['down_vote'])) {
            $this->assertEquals(-1, $actions['down_vote']['default_value']);
        }
        if (isset($actions['facebook_like'])) {
            $this->assertEquals(1, $actions['facebook_like']['default_value']);
        }
    }

    public function testGetConfigWithOverrideValues()
    {
        $configs = array(
            'actions' => array(
                'up_vote' => array(
                    'default_value' => '2',
                ),
                'down_vote' => array(
                    'default_value' => '-5',
                )
            ),
        );

        $container = $this->getContainer();
        $this->extension->load(array($configs), $container);
        $this->assertTrue($container->hasParameter($this->root . ".actions"));
        $this->assertTrue(is_array($container->getParameter($this->root . ".actions")));

        $actions = $container->getParameter($this->root . ".actions");
        if (isset($actions['up_vote'])) {
            $this->assertEquals(2, $actions['up_vote']['default_value']);
        }
        if (isset($actions['down_vote'])) {
            $this->assertEquals(-5, $actions['down_vote']['default_value']);
        }
        if (isset($actions['facebook_like'])) {
            $this->assertEquals(1, $actions['facebook_like']['default_value']);
        }
    }

    /**
     * Returns the Configuration to test
     *
     * @return Configuration
     */
    protected function getExtension()
    {
        return new KunstmaanVotingExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getContainer()
    {
        return new ContainerBuilder();
    }
}
