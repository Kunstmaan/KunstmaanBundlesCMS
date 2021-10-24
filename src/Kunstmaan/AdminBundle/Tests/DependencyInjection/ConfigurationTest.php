<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\Configuration;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Service\AuthenticationMailer\SwiftmailerService;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Config\Definition\BaseNode;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ExpectDeprecationTrait;
    use ConfigurationTestCaseTrait;

    private const DEFAULT_EXPECTED_CONFIG = [
        'website_title' => null,
        'multi_language' => true,
        'required_locales' => null,
        'default_locale' => null,
        'admin_prefix' => 'admin',
        'admin_locales' => ['en'],
        'session_security' => [
            'ip_check' => false,
            'user_agent_check' => false,
        ],
        'default_admin_locale' => 'en',
        'enable_console_exception_listener' => true,
        'enable_toolbar_helper' => '%kernel.debug%',
        'exception_logging' => [
            'enabled' => true,
            'exclude_patterns' => [],
        ],
        'toolbar_firewall_names' => ['main'],
        'admin_firewall_name' => 'main',
        'menu_items' => [],
        'password_restrictions' => [
            'min_digits' => null,
            'min_uppercase' => null,
            'min_special_characters' => null,
            'min_length' => null,
            'max_length' => null,
        ],
        'authentication' => [
            'enable_new_authentication' => true,
            'user_class' => User::class,
            'group_class' => Group::class,
            'mailer' => [
                'service' => SwiftmailerService::class,
                'from_address' => 'kunstmaancms@myproject.dev',
                'from_name' => 'Kunstmaan CMS',
            ],
        ],
    ];

    protected function getConfiguration(): ConfigurationInterface
    {
        return new Configuration();
    }

    public function testConfigGeneratesAsExpected()
    {
        $array = [
            'website_title' => null,
            'multi_language' => true,
            'required_locales' => null,
            'default_locale' => null,
            'admin_password' => 'l3tM31n!',
            'admin_locales' => ['nl'],
            'session_security' => [
                'ip_check' => false,
                'user_agent_check' => false,
            ],
            'default_admin_locale' => 'en',
            'enable_console_exception_listener' => true,
            'menu_items' => [],
            'admin_prefix' => 'admin',
            'enable_toolbar_helper' => false,
            'admin_firewall_name' => 'main',
            'password_restrictions' => [
                'min_digits' => 2,
                'min_uppercase' => 2,
                'min_special_characters' => 2,
                'min_length' => 16,
                'max_length' => 26,
            ],
        ];

        $expected = array_merge(self::DEFAULT_EXPECTED_CONFIG, [
            'admin_locales' => ['nl'],
            'admin_password' => 'l3tM31n!',
            'enable_toolbar_helper' => false,
            'password_restrictions' => [
                'min_digits' => 2,
                'min_uppercase' => 2,
                'min_special_characters' => 2,
                'min_length' => 16,
                'max_length' => 26,
            ],
        ]);
        $expected['authentication']['enable_new_authentication'] = true;

        $this->assertProcessedConfigurationEquals([$array], $expected);
    }

    /**
     * @group legacy
     */
    public function testDeprecatedAuthenticationConfig()
    {
        if (method_exists(BaseNode::class, 'getDeprecation')) {
            $this->expectDeprecation('Since kunstmaan/admin-bundle 6.1: The "kunstmaan_admin.authentication.enable_new_authentication" configuration key has been deprecated, remove it from your config.');
        } else {
            //NEXT_MAJOR remove else when symfony 4.4 support is removed
            $this->expectDeprecation('The "kunstmaan_admin.authentication.enable_new_authentication" configuration key has been deprecated, remove it from your config.');
        }

        $array = [
            'website_title' => null,
            'multi_language' => true,
            'required_locales' => null,
            'default_locale' => null,
            'session_security' => [
                'ip_check' => false,
                'user_agent_check' => false,
            ],
            'authentication' => [
                'enable_new_authentication' => true,
            ],
            'default_admin_locale' => 'en',
            'enable_console_exception_listener' => true,
            'menu_items' => [],
            'admin_prefix' => 'admin',
            'admin_firewall_name' => 'main',
        ];

        $this->assertProcessedConfigurationEquals([$array], self::DEFAULT_EXPECTED_CONFIG);
    }
}
