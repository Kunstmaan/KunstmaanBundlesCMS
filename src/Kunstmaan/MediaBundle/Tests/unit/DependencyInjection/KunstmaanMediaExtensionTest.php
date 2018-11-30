<?php

namespace Kunstmaan\MediaBundle\Tests\DependencyInjection;

use Kunstmaan\MediaBundle\DependencyInjection\KunstmaanMediaExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanMediaExtensionTest
 */
class KunstmaanMediaExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanMediaExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('twig.form.resources', []);
        $this->load(['enable_pdf_preview' => true]);

        $this->assertContainerBuilderHasParameter('twig.form.resources');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.soundcloud_api_key', 'YOUR_CLIENT_ID');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.remote_video');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.enable_pdf_preview', true);
        $this->assertContainerBuilderHasParameter('kunstmaan_media.blacklisted_extensions');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_manager.class', 'Kunstmaan\MediaBundle\Helper\MediaManager');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.folder_manager.class', 'Kunstmaan\MediaBundle\Helper\FolderManager');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.menu.adaptor.class', 'Kunstmaan\MediaBundle\Helper\Menu\MediaMenuAdaptor');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.listener.doctrine.class', 'Kunstmaan\MediaBundle\EventListener\DoctrineMediaListener');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.form.type.media.class', 'Kunstmaan\MediaBundle\Form\Type\MediaType');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.form.type.iconfont.class', 'Kunstmaan\MediaBundle\Form\Type\IconFontType');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.icon_font_manager.class', 'Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.icon_font.default_loader.class', 'Kunstmaan\MediaBundle\Helper\IconFont\DefaultIconFontLoader');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_creator_service.class', 'Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.mimetype_guesser.factory.class', 'Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactory');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.extension_guesser.factory.class', 'Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactory');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.validator.has_guessable_extension.class', 'Kunstmaan\MediaBundle\Validator\Constraints\HasGuessableExtensionValidator');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_handler.remote_slide.class', 'Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHandler');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_handler.remote_video.class', 'Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHandler');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_handler.remote_audio.class', 'Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHandler');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_handler.image.class', 'Kunstmaan\MediaBundle\Helper\Image\ImageHandler');
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_handler.file.class', 'Kunstmaan\MediaBundle\Helper\File\FileHandler');
        $this->assertContainerBuilderHasParameter('aviary_api_key', null);
        $this->assertContainerBuilderHasParameter('kunstmaan_media.media_path', '/uploads/media/');
        $this->assertContainerBuilderHasParameter('liip_imagine.filter.loader.background.class', 'Kunstmaan\MediaBundle\Helper\Imagine\BackgroundFilterLoader');
        $this->assertContainerBuilderHasParameter('liip_imagine.cache.manager.class', 'Kunstmaan\MediaBundle\Helper\Imagine\CacheManager');
        $this->assertContainerBuilderHasParameter('liip_imagine.cache.resolver.web_path.class', 'Kunstmaan\MediaBundle\Helper\Imagine\WebPathResolver');
        $this->assertContainerBuilderHasParameter('liip_imagine.controller.class', 'Kunstmaan\MediaBundle\Helper\Imagine\ImagineController');
    }
}
