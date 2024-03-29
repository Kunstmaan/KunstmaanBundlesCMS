<?php

namespace Kunstmaan\UserManagementBundle\Tests\DependencyInjection;

use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\UserManagementBundle\DependencyInjection\Configuration;
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
        $array = [];

        $this->assertProcessedConfigurationEquals([$array], [
            'user' => ['adminlist_configurator' => UserAdminListConfigurator::class],
        ]);
    }
}
