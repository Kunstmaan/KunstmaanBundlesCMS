<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\TranslatorBundle\Model\Export\ExportCommand;
use Kunstmaan\TranslatorBundle\Model\Import\ImportCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TranslatorCommandController extends Controller
{
    /**
     * @Route("/clear-cache", name="KunstmaanTranslatorBundle_command_clear_cache")
     */
    public function clearCacheAction()
    {
        $this->get('kunstmaan_translator.service.translator.resource_cacher')->flushCache();
        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_translator.command.clear.flash.success')
        );

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
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

        $this->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_translator.command.import.flash.success')
        );

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
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

        $this->get('kunstmaan_translator.service.importer.command_handler')->executeImportCommand($importCommand);

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->get('translator')->trans('kuma_translator.command.import.flash.force_success')
        );

        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
    }

    /**
     * @Route("/export", name="KunstmaanTranslatorBundle_command_export")
     */
    public function exportAction()
    {
        $locales = $this->getParameter('kuma_translator.managed_locales');
        $exportCommand = new ExportCommand();
        $exportCommand
            ->setLocales($locales)
            ->setFormat('csv');

        $response = $this->get('kunstmaan_translator.service.exporter.command_handler')->executeExportCSVCommand($exportCommand);

        return $response;
    }
}
