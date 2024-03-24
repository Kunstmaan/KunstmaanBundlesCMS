<?php

namespace Kunstmaan\RedirectBundle\Tests\DependencyInjection;

use Kunstmaan\RedirectBundle\DependencyInjection\Configuration;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    protected function getConfiguration(): \Symfony\Component\Config\Definition\ConfigurationInterface
    {
        return new Configuration();
    }

    public function testProcessedValueContainsRequiredValue()
    {
        $array = [
            'enable_improved_router' => true,
        ];

        $this->assertProcessedConfigurationEquals([$array], [
            'redirect_entity' => Redirect::class,
            'enable_improved_router' => true,
        ]);
    }
}
