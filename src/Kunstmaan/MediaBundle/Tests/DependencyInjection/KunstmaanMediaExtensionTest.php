<?php

namespace Kunstmaan\MediaBundle\Tests\DependencyInjection;

use Kunstmaan\MediaBundle\DependencyInjection\KunstmaanMediaExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanMediaExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
    {
        return [new KunstmaanMediaExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->load(['enable_pdf_preview' => true]);

        $this->assertContainerBuilderHasParameter('kunstmaan_media.soundcloud_api_key', 'YOUR_CLIENT_ID');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.remote_video');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.enable_pdf_preview', true);
        $this->assertContainerBuilderHasParameter('kunstmaan_media.blacklisted_extensions');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.aviary_api_key', null);
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_path', '/uploads/media/');
        $this->assertContainerBuilderHasParameter('liip_imagine.filter.loader.background.class', 'Kunstmaan\MediaBundle\Helper\Imagine\BackgroundFilterLoader');
    }

    public function testAviaryApiKeyWithNoConfig()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_media.aviary_api_key', null);
    }

    /**
     * @group legacy
     * @expectedDeprecation Not providing a value for the "kunstmaan_media.aviary_api_key" config while setting the "aviary_api_key" parameter is deprecated since KunstmaanMediaBundle 5.2, this config value will replace the "aviary_api_key" parameter in KunstmaanMediaBundle 6.0.
     */
    public function testAviaryApiKeyWithParameterSet()
    {
        $this->setParameter('aviary_api_key', 'api_key');

        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_media.aviary_api_key', 'api_key');
    }

    /**
     * @group legacy
     * @expectedDeprecation The child node "aviary_api_key" at path "kunstmaan_media" is deprecated. Because the aviary service is discontinued.
     */
    public function testAviaryApiKeyWithParameterAndConfigSet()
    {
        $this->setParameter('aviary_api_key', 'api_key');

        $this->load(['aviary_api_key' => 'other_api_key']);

        $this->assertContainerBuilderHasParameter('kunstmaan_media.aviary_api_key', 'other_api_key');
    }
}
