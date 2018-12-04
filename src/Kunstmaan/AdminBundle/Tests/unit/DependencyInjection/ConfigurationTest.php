<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit_Framework_TestCase;

/**
 * Class ConfigurationTest
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    /**
     * @return \Symfony\Component\Config\Definition\ConfigurationInterface
     */
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'admin_password' => 'l3tM31n!',
            'admin_locales' => [],
            'session_security' => [
                'ip_check' => false,
                'user_agent_check' => false,
            ],
            'default_admin_locale' => 'en',
            'enable_console_exception_listener' => true,
            'menu_items' => [],
            'google_signin' => [
                'client_id' => '7474505B',
                'client_secret' => '7474505B',
                'enabled' => true,
                'hosted_domains' => [],
            ],
            'admin_prefix' => 'admin',
            'admin_exception_excludes' => ['404'],
            'enable_toolbar_helper' => false,
            'provider_keys' => [],
            'toolbar_firewall_names' => [],
            'admin_firewall_name' => 'main',
            'password_restrictions' => [
                'min_digits' => 2,
                'min_uppercase' => 2,
                'min_special_characters' => 2,
                'min_length' => 16,
                'max_length' => 26,
            ],
        ];

        $this->assertProcessedConfigurationEquals([$array], $array);

        $array['google_signin']['enabled'] = false;
        $check = $array;
        $check['google_signin']['client_id'] = null;
        $check['google_signin']['client_secret'] = null;

        $this->assertProcessedConfigurationEquals([$array], $check);
    }

    public function testConfigDoesntGenerateAsExpected()
    {
        $array = [
            'admin_password' => 'l3tM31n!',
            'admin_locales' => [],
            'session_security' => [
                'ip_check' => false,
                'user_agent_check' => false,
            ],
            'default_admin_locale' => 'en',
            'enable_console_exception_listener' => true,
            'menu_items' => [],
            'google_signin' => [
                'enabled' => true,
                'hosted_domains' => [],
            ],
        ];

        $this->assertPartialConfigurationIsInvalid([$array], 'google_signin');
    }
}
