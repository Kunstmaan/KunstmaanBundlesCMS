<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\ImportCommandHandler;
use Kunstmaan\TranslatorBundle\Service\Translator\ResourceCacher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class TranslatorCommandController extends AbstractController
{
    /** @var ResourceCacher */
    private $resourceCacher;
    /** @var ImportCommandHandler */
    private $importCommandHandler;
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(ResourceCacher $resourceCacher, ImportCommandHandler $importCommandHandler, TranslatorInterface $translator)
    {
        $this->resourceCacher = $resourceCacher;
        $this->importCommandHandler = $importCommandHandler;
        $this->translator = $translator;
    }

    /**
     * @Route("/clear-cache", name="KunstmaanTranslatorBundle_command_clear_cache")
     */
    public function clearCacheAction()
    {
        $this->resourceCacher->flushCache();

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->translator->trans('kuma_translator.command.clear.flash.success')
        );

        return $this->redirectToRoute('KunstmaanTranslatorBundle_settings_translations');
    }

    /**
     * @Route("/import", name="KunstmaanTranslatorBundle_command_import")
     */
    public function importAction()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(false)
            ->setDefaultBundle($this->getParameter('kuma_translator.default_bundle'))
            ->setBundles($this->getParameter('kuma_translator.bundles'))
            ->setGlobals(true);

        $this->importCommandHandler->executeImportCommand($importCommand);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->translator->trans('kuma_translator.command.import.flash.success')
        );

        return $this->redirectToRoute('KunstmaanTranslatorBundle_settings_translations');
    }

    /**
     * @Route("/import-forced", name="KunstmaanTranslatorBundle_command_import_forced")
     */
    public function importForcedAction()
    {
        $importCommand = new ImportCommand();
        $importCommand
            ->setForce(true)
            ->setDefaultBundle($this->getParameter('kuma_translator.default_bundle'))
            ->setBundles($this->getParameter('kuma_translator.bundles'))
            ->setGlobals(false);

        $this->importCommandHandler->executeImportCommand($importCommand);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->translator->trans('kuma_translator.command.import.flash.force_success')
        );

        return $this->redirectToRoute('KunstmaanTranslatorBundle_settings_translations');
    }
}
