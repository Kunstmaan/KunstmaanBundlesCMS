<?php

namespace Kunstmaan\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class DeprecateClassParametersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $expectedValues = [
            'kunstmaan_media.media_handler.remote_slide.class' => \Kunstmaan\MediaBundle\Helper\RemoteSlide\RemoteSlideHandler::class,
            'kunstmaan_media.media_handler.remote_video.class' => \Kunstmaan\MediaBundle\Helper\RemoteVideo\RemoteVideoHandler::class,
            'kunstmaan_media.media_handler.remote_audio.class' => \Kunstmaan\MediaBundle\Helper\RemoteAudio\RemoteAudioHandler::class,
            'kunstmaan_media.media_handler.image.class' => \Kunstmaan\MediaBundle\Helper\Image\ImageHandler::class,
            'kunstmaan_media.media_handler.file.class' => \Kunstmaan\MediaBundle\Helper\File\FileHandler::class,
            'kunstmaan_media.pdf_transformer.class' => \Kunstmaan\MediaBundle\Helper\Transformer\PdfTransformer::class,
            'kunstmaan_media.media_handler.pdf.class' => \Kunstmaan\MediaBundle\Helper\File\PdfHandler::class,
            'kunstmaan_media.media_manager.class' => \Kunstmaan\MediaBundle\Helper\MediaManager::class,
            'kunstmaan_media.folder_manager.class' => \Kunstmaan\MediaBundle\Helper\FolderManager::class,
            'kunstmaan_media.menu.adaptor.class' => \Kunstmaan\MediaBundle\Helper\Menu\MediaMenuAdaptor::class,
            'kunstmaan_media.listener.doctrine.class' => \Kunstmaan\MediaBundle\EventListener\DoctrineMediaListener::class,
            'kunstmaan_media.form.type.media.class' => \Kunstmaan\MediaBundle\Form\Type\MediaType::class,
            'kunstmaan_media.form.type.iconfont.class' => \Kunstmaan\MediaBundle\Form\Type\IconFontType::class,
            'kunstmaan_media.icon_font_manager.class' => \Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager::class,
            'kunstmaan_media.icon_font.default_loader.class' => \Kunstmaan\MediaBundle\Helper\IconFont\DefaultIconFontLoader::class,
            'kunstmaan_media.media_creator_service.class' => \Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService::class,
            'kunstmaan_media.mimetype_guesser.factory.class' => \Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactory::class,
            'kunstmaan_media.extension_guesser.factory.class' => \Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactory::class,
            'kunstmaan_media.validator.has_guessable_extension.class' => \Kunstmaan\MediaBundle\Validator\Constraints\HasGuessableExtensionValidator::class,
        ];

        foreach ($expectedValues as $parameter => $expectedValue) {
            if (false === $container->hasParameter($parameter)) {
                continue;
            }

            $currentValue = $container->getParameter($parameter);
            if ($currentValue !== $expectedValue) {
                @trigger_error(sprintf('Using the "%s" parameter to change the class of the service definition is deprecated in KunstmaanMediaBundle 5.2 and will be removed in KunstmaanMediaBundle 6.0. Use service decoration or a service alias instead.', $parameter), E_USER_DEPRECATED);
            }
        }
    }
}
