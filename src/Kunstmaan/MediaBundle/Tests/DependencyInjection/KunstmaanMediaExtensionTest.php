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
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_path', '/uploads/media/');
        $this->assertContainerBuilderHasParameter('liip_imagine.filter.loader.background.class', 'Kunstmaan\MediaBundle\Helper\Imagine\BackgroundFilterLoader');
    }
}
