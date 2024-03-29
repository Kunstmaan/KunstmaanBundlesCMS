<?php

namespace Kunstmaan\VotingBundle\Tests\DependencyInjection;

use Kunstmaan\VotingBundle\DependencyInjection\KunstmaanVotingExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanVotingExtensionTest extends TestCase
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

    public function setUp(): void
    {
        parent::setUp();

        $this->extension = $this->getExtension();
        $this->root = 'kuma_voting';
    }

    public function testGetConfigWithOverrideDefaultValue()
    {
        $container = $this->getContainer();
        $container->setParameter('voting_default_value', 2);
        $this->extension->load([['actions' => [['name' => 'foo', 'max_number_by_ip' => 2]]]], $container);
        $this->assertTrue($container->hasParameter($this->root . '.actions'));
        $this->assertIsArray($container->getParameter($this->root . '.actions'));

        $actions = $container->getParameter($this->root . '.actions');
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
        $this->extension->load([[]], $container);
        $this->assertTrue($container->hasParameter($this->root . '.actions'));
        $this->assertIsArray($container->getParameter($this->root . '.actions'));

        $actions = $container->getParameter($this->root . '.actions');
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
        $configs = [
            'actions' => [
                'up_vote' => [
                    'default_value' => '2',
                ],
                'down_vote' => [
                    'default_value' => '-5',
                ],
            ],
        ];

        $container = $this->getContainer();
        $this->extension->load([$configs], $container);
        $this->assertTrue($container->hasParameter($this->root . '.actions'));
        $this->assertIsArray($container->getParameter($this->root . '.actions'));

        $actions = $container->getParameter($this->root . '.actions');
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

    protected function getExtension(): KunstmaanVotingExtension
    {
        return new KunstmaanVotingExtension();
    }

    private function getContainer(): ContainerBuilder
    {
        return new ContainerBuilder();
    }
}
