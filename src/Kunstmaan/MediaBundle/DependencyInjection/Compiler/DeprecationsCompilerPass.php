<?php

namespace Kunstmaan\MediaBundle\DependencyInjection\Compiler;

use Kunstmaan\MediaBundle\Command\CleanDeletedMediaCommand;
use Kunstmaan\MediaBundle\Command\RebuildFolderTreeCommand;
use Kunstmaan\MediaBundle\EventListener\DoctrineMediaListener;
use Kunstmaan\MediaBundle\Form\Type\IconFontType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\MediaBundle\Helper\ExtensionGuesserFactory;
use Kunstmaan\MediaBundle\Helper\FolderManager;
use Kunstmaan\MediaBundle\Helper\IconFont\DefaultIconFontLoader;
use Kunstmaan\MediaBundle\Helper\IconFont\IconFontManager;
use Kunstmaan\MediaBundle\Helper\MediaManager;
use Kunstmaan\MediaBundle\Helper\Menu\MediaMenuAdaptor;
use Kunstmaan\MediaBundle\Helper\MimeTypeGuesserFactory;
use Kunstmaan\MediaBundle\Helper\Services\MediaCreatorService;
use Kunstmaan\MediaBundle\Repository\FolderRepository;
use Kunstmaan\MediaBundle\Validator\Constraints\HasGuessableExtensionValidator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DeprecationsCompilerPass
 *
 * @package Kunstmaan\MediaBundle\DependencyInjection\Compiler
 */
class DeprecationsCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_media.media_manager', MediaManager::class],
                ['kunstmaan_media.listener.doctrine', DoctrineMediaListener::class],
                ['form.type.media', MediaType::class],
                ['form.type.iconfont', IconFontType::class],
                ['kunstmaan_media.icon_font_manager', IconFontManager::class],
                ['kunstmaan_media.icon_font.default_loader', DefaultIconFontLoader::class],
                ['kunstmaan_media.media_creator_service', MediaCreatorService::class],
                ['kunstmaan_media.repository.folder', FolderRepository::class],
                ['kunstmaan_media.menu.adaptor', MediaMenuAdaptor::class],
                ['kunstmaan_media.folder_manager', FolderManager::class],
                ['kunstmaan_media.mimetype_guesser.factory', MimeTypeGuesserFactory::class],
                ['kunstmaan_media.extension_guesser.factory', ExtensionGuesserFactory::class],
                ['kunstmaan_media.command.rebuildfoldertree', RebuildFolderTreeCommand::class],
                ['kunstmaan_media.command.cleandeletedmedia', CleanDeletedMediaCommand::class],
                ['kunstmaan_media.validator.has_guessable_extension', HasGuessableExtensionValidator::class],
            ]
        );

        $this->addDeprecatedChildDefinitions(
            $container,
            [
                ['kunstmaan_media.media_manager.class', MediaManager::class],
                ['kunstmaan_media.folder_manager.class', FolderManager::class],
                ['kunstmaan_media.menu.adaptor.class', MediaMenuAdaptor::class],
                ['kunstmaan_media.listener.doctrine.class', DoctrineMediaListener::class],
                ['kunstmaan_media.form.type.media.class', MediaType::class],
                ['kunstmaan_media.form.type.iconfont.class', IconFontType::class],
                ['kunstmaan_media.icon_font_manager.class', IconFontManager::class],
                ['kunstmaan_media.icon_font.default_loader.class', DefaultIconFontLoader::class],
                ['kunstmaan_media.media_creator_service.class', MediaCreatorService::class],
                ['kunstmaan_media.mimetype_guesser.factory.class', MimeTypeGuesserFactory::class],
                ['kunstmaan_media.extension_guesser.factory.class', ExtensionGuesserFactory::class],
                ['kunstmaan_media.validator.has_guessable_extension.class', HasGuessableExtensionValidator::class],
            ],
            true
        );
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $deprecations
     * @param bool             $parametered
     */
    private function addDeprecatedChildDefinitions(ContainerBuilder $container, array $deprecations, $parametered = false)
    {
        foreach ($deprecations as $deprecation) {
            // Don't allow service with same name as class.
            if ($parametered && $container->getParameter($deprecation[0]) === $deprecation[1]) {
                continue;
            }

            $definition = new ChildDefinition($deprecation[1]);
            if (isset($deprecation[2])) {
                $definition->setPublic($deprecation[2]);
            }

            if ($parametered) {
                $class = $container->getParameter($deprecation[0]);
                $definition->setClass($class);
                $definition->setDeprecated(
                    true,
                    'Override service class with "%service_id%" is deprecated since KunstmaanMediaBundle 5.1 and will be removed in 6.0. Override the service instance instead.'
                );
                $container->setDefinition($class, $definition);
            } else {
                $definition->setClass($deprecation[1]);
                $definition->setDeprecated(
                    true,
                    'Passing a "%service_id%" instance is deprecated since KunstmaanMediaBundle 5.1 and will be removed in 6.0. Use the FQCN instead.'
                );
                $container->setDefinition($deprecation[0], $definition);
            }
        }
    }
}
