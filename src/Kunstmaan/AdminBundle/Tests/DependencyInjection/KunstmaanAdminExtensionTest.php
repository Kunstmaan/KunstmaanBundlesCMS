<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanAdminExtensionTest extends AbstractExtensionTestCase
{
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
            'admin_password' => 'omgchangethis',
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

        $this->assertContainerBuilderHasParameter('version_checker.url', 'https://cms.kunstmaan.be/version-check');
        $this->assertContainerBuilderHasParameter('version_checker.timeframe', 60 * 60 * 24);
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.admin_locales');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_admin_locale');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.ip_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.user_agent_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.enabled');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.client_id');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.client_secret');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.hosted_domains');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.website_title" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     */
    public function testWebsiteTitleWithParameterSet()
    {
        $this->setParameter('websitetitle', 'Mywebsite');

        $this->load($this->getRequiredConfig('website_title'));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.website_title', 'Mywebsite');
    }

    public function testWebsiteTitleWithParameterAndConfigSet()
    {
        $this->setParameter('websitetitle', 'Mywebsite');

        $this->load(array_merge($this->getRequiredConfig(), ['website_title' => 'My real website']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.website_title', 'My real website');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.multi_language" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     */
    public function testMultiLanguageWithParameterSet()
    {
        $this->setParameter('multilanguage', true);

        $this->load($this->getRequiredConfig('multi_language'));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    public function testMultiLanguageWithParameterAndConfigSet()
    {
        $this->setParameter('multilanguage', false);

        $this->load(array_merge($this->getRequiredConfig(), ['multi_language' => true]));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    /**
     * @group legacy
     */
    public function testMultiLanguageScalarParameter()
    {
        $this->setParameter('multilanguage', true);

        $this->load(array_merge($this->getRequiredConfig(), ['multi_language' => 'randomvalue']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.required_locales" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     */
    public function testRequiredLocalesWithParameterSet()
    {
        $this->setParameter('requiredlocales', 'nl|en');

        $this->load($this->getRequiredConfig('required_locales'));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.required_locales', 'nl|en');
    }

    public function testRequiredLocalesWithParameterAndConfigSet()
    {
        $this->setParameter('requiredlocales', 'nl|en');

        $this->load(array_merge($this->getRequiredConfig(), ['required_locales' => 'nl|en|fr']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.required_locales', 'nl|en|fr');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.default_locale" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     */
    public function testDefaultLocaleWithParameterSet()
    {
        $this->setParameter('defaultlocale', 'en');

        $this->load($this->getRequiredConfig('default_locale'));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_locale', 'en');
    }

    public function testDefaultLocaleWithParameterAndConfigSet()
    {
        $this->setParameter('defaultlocale', 'en');

        $this->load(array_merge($this->getRequiredConfig(), ['default_locale' => 'nl']));

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_locale', 'nl');
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.website_title" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.multi_language" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.required_locales" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     * @expectedDeprecation Not providing a value for the "kunstmaan_admin.default_locale" config is deprecated since KunstmaanAdminBundle 5.2, this config value will be required in KunstmaanAdminBundle 6.0.
     */
    public function testLegacyParameters()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.website_title', '');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', '');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.required_locales', '');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_locale', '');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Some parameters required for the admin extension
        $this->container->setParameter('kernel.logs_dir', '/somewhere/over/the/rainbow');
        $this->container->setParameter('kernel.environment', 'staging');
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
