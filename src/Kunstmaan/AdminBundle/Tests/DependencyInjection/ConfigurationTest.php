<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\Configuration;
use Kunstmaan\AdminBundle\Entity\Group;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Service\AuthenticationMailer\SwiftmailerService;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigurationTest extends TestCase
{
    use ExpectDeprecationTrait;
    use ConfigurationTestCaseTrait;

    private const DEFAULT_EXPECTED_CONFIG = [
        'website_title' => null,
        'multi_language' => null,
        'required_locales' => null,
        'default_locale' => null,
        'admin_prefix' => 'admin',
        'admin_locales' => ['en'],
        'session_security' => [
            'ip_check' => false,
            'user_agent_check' => false,
        ],
        'admin_exception_excludes' => [],
        'default_admin_locale' => 'en',
        'enable_console_exception_listener' => true,
        'enable_toolbar_helper' => '%kernel.debug%',
        'exception_logging' => [
            'enabled' => true,
            'exclude_patterns' => [],
        ],
        'provider_keys' => [],
        'toolbar_firewall_names' => ['main'],
        'admin_firewall_name' => 'main',
        'menu_items' => [],
        'google_signin' => [
            'enabled' => false,
            'client_id' => null,
            'client_secret' => null,
            'hosted_domains' => [],
        ],
        'password_restrictions' => [
            'min_digits' => null,
            'min_uppercase' => null,
            'min_special_characters' => null,
            'min_length' => null,
            'max_length' => null,
        ],
        'authentication' => [
            'enable_new_authentication' => false,
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
            'multi_language' => null,
            'required_locales' => null,
            'default_locale' => null,
            'authentication' => [
                'enable_new_authentication' => true,
            ],
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
    public function testDeprecatedGoogleSigninConfig()
    {
        $this->expectDeprecation('The "kunstmaan_admin.google_signin" option is deprecated. The google oauth authenication will be removed in KusntmaanAdminBundle 6.0.');

        $array = [
            'website_title' => null,
            'multi_language' => null,
            'required_locales' => null,
            'default_locale' => null,
            'authentication' => [
                'enable_new_authentication' => true,
            ],
            'admin_password' => 'l3tM31n!',
            'admin_locales' => ['nl'],
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
            'google_signin' => [
                'enabled' => true,
                'client_id' => '7474505B',
                'client_secret' => '7474505B',
                'hosted_domains' => [],
            ],
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

    /**
     * @group legacy
     */
    public function testDepecratedExceptionExcludes()
    {
        $this->expectDeprecation('The "admin_exception_excludes" option is deprecated. Use "kunstmaan_admin.exception_logging.exclude_patterns" instead.');

        $config = [
            'admin_exception_excludes' => ['404'],
        ];
        $expected = array_merge(self::DEFAULT_EXPECTED_CONFIG, [
            'admin_exception_excludes' => ['404'],
        ]);

        $this->assertProcessedConfigurationEquals([$config], $expected);
    }
}
