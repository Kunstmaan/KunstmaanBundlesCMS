<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanAdminExtensionTest extends AbstractExtensionTestCase
{
    use ExpectDeprecationTrait;

    protected function setUp(): void
    {
        parent::setUp();

        // Some parameters required for the admin extension
        $this->container->setParameter('kernel.logs_dir', '/somewhere/over/the/rainbow');
        $this->container->setParameter('kernel.environment', 'staging');
    }

    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanAdminExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load([
            'dashboard_route' => true,
            'menu_items' => [
                [
                    'route' => 'route66',
                    'label' => 'Route 66',
                ],
            ],
            'website_title' => 'Example title',
            'multi_language' => true,
            'required_locales' => 'nl|fr|en',
            'default_locale' => 'nl',
        ]);

        $this->assertContainerBuilderHasParameter('version_checker.url', 'https://kunstmaancms.be/version-check');
        $this->assertContainerBuilderHasParameter('version_checker.timeframe', 60 * 60 * 24);
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.admin_locales');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_admin_locale');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.ip_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.user_agent_check');
    }

    public function testWebsiteTitleWithParameterAndConfigSet()
    {
        $this->load(array_merge($this->getRequiredConfig(), ['website_title' => 'My real website']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.website_title', 'My real website');
    }

    public function testMultiLanguageWithParameterAndConfigSet()
    {
        $this->load(array_merge($this->getRequiredConfig(), ['multi_language' => true]));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    public function testRequiredLocalesWithParameterAndConfigSet()
    {
        $this->load(array_merge($this->getRequiredConfig(), ['required_locales' => 'nl|en|fr']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.required_locales', 'nl|en|fr');
    }

    public function testDefaultLocaleWithParameterAndConfigSet()
    {
        $this->load(array_merge($this->getRequiredConfig(), ['default_locale' => 'nl']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_locale', 'nl');
    }

    public function testExceptionExcludesFromExceptionLoggingConfig()
    {
        $this->load(array_merge($this->getRequiredConfig(), [
            'exception_logging' => [
                'exclude_patterns' => ['test_exclude_new_config'],
            ],
        ]));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.admin_exception_excludes', ['test_exclude_new_config']);
    }

    /**
     * @group legacy
     */
    public function testDeprecatedAuthenticationEnableParameter()
    {
        $this->expectDeprecation('%SThe "kunstmaan_admin.authentication.enable_new_authentication" configuration key has been deprecated, remove it from your config.');

        $this->load(array_merge($this->getRequiredConfig(), ['authentication' => ['enable_new_authentication' => true]]));
    }

    private function getRequiredConfig(string $excludeKey = null)
    {
        $requiredConfig = [
            'website_title' => 'Example title',
            'multi_language' => true,
            'required_locales' => 'nl|fr|en',
            'default_locale' => 'nl',
        ];

        if (array_key_exists($excludeKey, $requiredConfig)) {
            unset($requiredConfig[$excludeKey]);
        }

        return $requiredConfig;
    }
}
