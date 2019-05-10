<?php

namespace Kunstmaan\AdminBundle\Tests\DependencyInjection;

use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

/**
 * Class KunstmaanAdminExtensionTest
 */
class KunstmaanAdminExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
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
        ]);

        $this->assertContainerBuilderHasParameter('version_checker.url', 'https://bundles.kunstmaan.be/version-check');
        $this->assertContainerBuilderHasParameter('version_checker.timeframe', (60 * 60 * 24));
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.admin_locales');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_admin_locale');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.ip_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.session_security.user_agent_check');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.enabled');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.client_id');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.client_secret');
        $this->assertContainerBuilderHasParameter('kunstmaan_admin.google_signin.hosted_domains');
    }

    public function testWebsiteTitleWithParameterSet()
    {
        $this->setParameter('websitetitle', 'Mywebsite');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.website_title', 'Mywebsite');
    }

    public function testWebsiteTitleWithParameterAndConfigSet()
    {
        $this->setParameter('websitetitle', 'Mywebsite');

        $this->load(['website_title' => 'My real website']);

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.website_title', 'My real website');
    }

    public function testMultiLanguageWithParameterSet()
    {
        $this->setParameter('multilanguage', true);

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    public function testMultiLanguageWithParameterAndConfigSet()
    {
        $this->setParameter('multilanguage', false);

        $this->load(['multi_language' => true]);

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    /**
     * @group legacy
     */
    public function testMultiLanguageScalarParameter()
    {
        $this->setParameter('multilanguage', true);

        $this->load(['multi_language' => 'randomvalue']);

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.multi_language', true);
    }

    public function testRequiredLocalesWithParameterSet()
    {
        $this->setParameter('requiredlocales', 'nl|en');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.required_locales', 'nl|en');
    }

    public function testRequiredLocalesWithParameterAndConfigSet()
    {
        $this->setParameter('requiredlocales', 'nl|en');

        $this->load(['required_locales' => 'nl|en|fr']);

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.required_locales', 'nl|en|fr');
    }

    public function testDefaultLocaleWithParameterSet()
    {
        $this->setParameter('defaultlocale', 'en');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_admin.default_locale', 'en');
    }

    public function testDefaultLocaleWithParameterAndConfigSet()
    {
        $this->setParameter('defaultlocale', 'en');

        $this->load(['default_locale' => 'nl']);

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

    protected function setUp()
    {
        parent::setUp();

        // Some parameters required for the admin extension
        $this->container->setParameter('kernel.logs_dir', '/somewhere/over/the/rainbow');
        $this->container->setParameter('kernel.environment', 'staging');
    }
}
