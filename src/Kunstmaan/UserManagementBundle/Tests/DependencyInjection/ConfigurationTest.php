<?php

namespace Kunstmaan\UserManagementBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Kunstmaan\UserManagementBundle\AdminList\UserAdminListConfigurator;
use Kunstmaan\UserManagementBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return ConfigurationInterface
     */
    protected function getConfiguration()
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
